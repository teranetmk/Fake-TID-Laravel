<?php

namespace App\Http\Controllers\Shop;

use Throwable;
use Validator;
use Carbon\Carbon;

use App\Models\FAQ;

use App\Models\Tid;

use App\Models\Product;
use App\Models\Setting;
use setasign\Fpdi\Fpdi;
use App\Models\UserOrder;
use App\Models\Packstation;
use App\Models\ProductItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\DeliveryMethod;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Models\TidPackStation;
use App\Models\User;
use App\Services\Domain\TidGenerationService;
use App\Services\Domain\BitcoinConverterService;
use BADDIServices\FakeTIDs\Events\Order\OrderWasCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ShopController extends Controller
{
    /** @var \App\Services\Domain\TidGenerationService */
    private $tidGenerationService;

    /** @var \App\Services\Domain\BitcoinConverterService */
    private $bitcoinConverterService;

    public function __construct(TidGenerationService $tidGenerationService, BitcoinConverterService $bitcoinConverterService)
    {
        if ( Setting::get( 'app.access_only_for_users', false ) ) {
            $this->middleware( 'auth' );
        }

        $this->tidGenerationService = $tidGenerationService;
        $this->bitcoinConverterService = $bitcoinConverterService;
    }

    public function showShopPage()
    {
        return redirect('/');
        $categories = ProductCategory::orderByDesc( 'created_at' )->get();

        return response()->view( 'frontend/shop.shop', [
            'categories' => $categories
        ], 200 )->header('Cache-Control', 'public, max-age: 900');
    }

    public function buyProductForm( Request $request, $pId = null, $pAmount = null )
    {
        if ( !Auth::check() ) {
            return redirect()->route( 'shop' )->with( [
                'errorMessage' => __( 'frontend/shop.must_logged_in' )
            ] );
        }
        
        $backAction = false;
        if ( $pId != null && $pAmount != null ) {
            $backAction = true;
        }

        if ( $request->getMethod() == 'POST' || $backAction ) {
            if ( $backAction ) {
                $productId = $pId;
            } else {
                $productId = $request->get( 'product_id' );
            }

            $product = Product::where( 'id', $productId )->get()->first();

            if ( $product == null ) {
                return redirect()->back()
                    ->with([
                        'errorMessage' => __( 'frontend/shop.product_not_found' )
                    ]);
            }

            $isRefundingProduct = in_array($product->name, ['LIT für Refund']);
            $isRandomProduct = in_array($product->name, ['LIT für Refund', 'LIT für Filling']);
            $isBoxingProduct = in_array(strtolower($product->name), ['nachnahme boxing']);
           
            if ( $backAction ) {
                $amount = $pAmount;
            } else {
                $amount = intval( $request->get( 'product_amount' ) );
            }

            if ($product->isDigitalGoods() && $amount < $product->getOrderMinimum()) {
               
                return redirect()->route( 'shop' )->with( [
                    'errorMessage' => sprintf('Die Mindestabnahme beträgt %d Accounts.', $product->getOrderMinimum())
                ] );
            } elseif ($product->isDigitalGoods() && ! $product->isAvailableAmount($amount)) {
                
                // return redirect()->back()->with( [
                //     'errorMessage' => 'Dieses Produkt ist ausverkauft oder die Bestellung überschreitet die vorhandene Menge.'
                // ] );
                return redirect()->back()->with( [
                    'errorMessage' => 'Ihre Bestellung überschreitet die Anzahl an verfügbaren Accounts.'
                ] );
            }

            if ($product->isUnlimited() && ! $product->isDigitalGoods()) {
                $amount = 1;
            } elseif ($product->asWeight() && $amount > $product->getWeightAvailable()) {
                $amount = $product->getWeightAvailable();
            } elseif (! $product->asWeight() && $amount > $product->getStock()) {
                $amount = $product->getStock();
            }

            if ( $amount <= 0 ) {
                return redirect()->route( 'shop' );
            }
            //dd($product->price_in_cent );
            $totalPrice = $isBoxingProduct ? 0 : ($product->price_in_cent * $amount);
            $totalPriceHtml = Product::formatPrice($totalPrice);
            // dd($product->category->slug);
            $replaceEntry = FAQ::where( 'id', Setting::get( 'shop.replace_rules' ) )->first();

            return view( 'frontend/shop.product_confirm_buy', [
                'product'        => $product,
                'amount'         => $amount,
                'totalPrice'     => $totalPrice,
                'totalPriceHtml' => $totalPriceHtml,
                'replaceEntry'   => $replaceEntry,
                'category' => $product->category->slug,
                'isRefundingProduct'    => $isRefundingProduct,
                'isBoxingProduct'       => $isBoxingProduct,
                'isRandomProduct'       => $isRandomProduct,
            ] );
        }

        return redirect()->route( 'shop' );
    }

    public function buyProductConfirmForm( Request $request )
    {
        
        if (! Auth::check()) {
            return redirect()
                ->route('shop')
                ->with( 
                    [
                        'errorMessage' => __( 'frontend/shop.must_logged_in' )
                    ]
                );
        }

        $productId = $request->get('product_id');
        if ($request->getMethod() == 'POST' && ! is_null($productId)) {
            $product   = Product::where('id', $productId)->first();
            if (! $product instanceof Product) {
                return redirect()
                    ->route( 'shop' )
                    ->with([
                        'errorMessage' => __( 'frontend/shop.product_not_found' )
                    ]);
            }

            $amount = intval($request->get('product_amount'));

            $isRefundingProduct = in_array($product->name, ['LIT für Refund']);
            $isRandomProduct = in_array($product->name, ['LIT für Refund', 'LIT für Filling']);
            $isBoxingProduct = in_array(strtolower($product->name), ['nachnahme boxing']);

            if (! $product->isDigitalGoods() && ! $isBoxingProduct) {
                $tid = Tid::where( 'product_id', $product->id )->where( 'used', 0 )->firstOrFail();
            }

            $validated_address = Validator::make( $request->all(), [
                'first_name'        => 'bail|required|max:255',
                'last_name'         => 'bail|required|max:255',
                'street'            => 'bail|required|max:255',
                'zip'               => 'bail|required|max:255',
                'city'              => 'bail|required|max:255',
                'country'           => 'bail|required|max:255',
                'sender_first_name' => 'bail|required|max:255',
                'sender_last_name'  => 'bail|required|max:255',
                'sender_street'     => 'bail|required|max:255',
                'sender_zip'        => 'bail|required|max:255',
                'sender_city'       => 'bail|required|max:255',
                'sender_country'    => 'bail|required|max:255',
                'shipping_time'     => 'bail|required',
                'send_at'           => 'bail|required_if:shipping_time,desired_date',
                'receipt'           => 'nullable|in:yes,no'
    
            ], [
    
                'first_name.required'        => __('frontend/shop.validator.first_name' ),
                'last_name.required'         => __('frontend/shop.validator.last_name' ),
                'street.required'            => __('frontend/shop.validator.street' ),
                'zip.required'               => __('frontend/shop.validator.zip' ),
                'city.required'              => __('frontend/shop.validator.city' ),
                'country.required'           => __('frontend/shop.validator.country' ),
                'sender_first_name.required' => __('frontend/shop.validator.sender_first_name' ),
                'sender_last_name.required'  => __('frontend/shop.validator.sender_last_name' ),
                'sender_street.required'     => __('frontend/shop.validator.sender_street' ),
                'sender_zip.required'        => __('frontend/shop.validator.sender_zip' ),
                'sender_city.required'       => __('frontend/shop.validator.sender_city' ),
                'sender_country.required'    => __('frontend/shop.validator.sender_country' ),
                'shipping_time.required'     => __('frontend/shop.validator.shipping_time' ),
                'send_at.required'           => __('frontend/shop.validator.send_at' ),
            ]);

            if ($isRefundingProduct) {
                $validated_address = Validator::make( $request->all(), [
                    'tracking_number'   => 'bail|required|max:255',
                    'shipping_time'     => 'bail|required',
                    'send_at'           => 'bail|required_if:shipping_time,desired_date',
                    'receipt'           => 'nullable|in:yes,no'
                ], [
                    'shipping_time.required'     => __('frontend/shop.validator.shipping_time' ),
                    'send_at.required'           => __('frontend/shop.validator.send_at' ),
                ]);
            }
            if ($isBoxingProduct & in_array($product->category->slug, ['welt-nachnahme','morty-nachnahme'])) {
                $validated_address = Validator::make( $request->all(), [
                    'product_name'                  => 'bail|required|max:255',
                    'product_size'                  => 'bail|required|max:255',
                    'product_weight'                => 'bail|required|max:255',
                    'product_payment_amount'        => 'bail|required|integer',
                    'product_package_labels_link'   => 'bail|required|max:255',
                    'amazon_product_link'           => 'bail|required|max:255',
                    'service_fee'                   => 'bail|required|integer',
                ]);
            }
            if ($isBoxingProduct & !in_array($product->category->slug, ['welt-nachnahme','morty-nachnahme'])) {
                $validated_address = Validator::make( $request->all(), [
                    'product_name'                  => 'bail|required|max:255',
                    'product_size'                  => 'bail|required|max:255',
                    'product_weight'                => 'bail|required|max:255',
                    'product_payment_amount'        => 'bail|required|integer|min:500',
                    'product_package_labels_link'   => 'bail|required|max:255',
                    'amazon_product_link'           => 'bail|required|max:255',
                    'service_fee'                   => 'bail|required|integer',
                ]);
            }
            
            if ($product->isDigitalGoods()) {
                $validated_address = Validator::make( $request->all(), [
                    'product_amount'    => 'integer|required|min:' . $product->getOrderMinimum(),
                ], [
                    'product_amount.*'  => sprintf('Die Mindestabnahme beträgt %d Accounts.', $product->getOrderMinimum())
                ]);
            }

            if ($validated_address->fails()) {
                return redirect()
                    ->route( 'buy-product', [ $productId, $amount ] )
                    ->withErrors( $validated_address)
                    ->withInput();
            }

            $dropInfo            = '';
            $status              = 'nothing';
            $deliveryMethodId    = 0;
            $deliveryMethodName  = "";
            $deliveryMethodPrice = 0;
            $extraCosts          = 0;


            if ( $product->dropNeeded() ) {

                $status           = 'pending';
                $deliveryMethodId = $request->get( 'product_delivery_method' ) ?? 0;
                $deliveryMethod   = DeliveryMethod::where( 'id', $deliveryMethodId )->get()->first();

                if ( $deliveryMethod == null && ! $product->isDigitalGoods() && ! $isBoxingProduct && ! $isRandomProduct) {

                    return redirect()->route( 'buy-product', [ $productId, $amount ] )->with( [
                        'errorMessage' => __( 'frontend/shop.delivery_method_needed' ),
                        'productDrop'  => $dropInfo
                    ] );

                } elseif (! $product->isDigitalGoods() && ! $isBoxingProduct && ! $isRandomProduct) {
                    $extraCosts          += $deliveryMethod->price;
                    $deliveryMethodName  = $deliveryMethod->name;
                    $deliveryMethodPrice = $deliveryMethod->price;
                }

                if ( $request->exists( 'shipping_time' ) && $request->shipping_time == 'desired_date' ) {
                    $extraCosts += 500;
                }

                // if ( $request->get( 'product_drop' ) == null ) {
                //
                //     return redirect()->route( 'buy-product', [ $productId, $amount ] )->with( [
                //         'errorMessage' => __( 'frontend/shop.order_note_needed' ),
                //         'productDrop'  => $dropInfo
                //     ] );
                //
                // } else if ( strlen( $request->get( 'product_drop' ) ) > 500 ) {
                //
                //     return redirect()->route( 'buy-product', [ $productId, $amount ] )->with( [
                //         'errorMessage' => __( 'frontend/shop.order_note_long', [
                //             'charallowed' => 500
                //         ] ),
                //         'productDrop'  => $dropInfo
                //     ] );
                //
                // } else {
                //
                //     $dropInfo = $request->get( 'product_drop' );
                //
                // }

            }

            try {
                if ($request->input('receipt') !== null && $request->input('receipt') === 'yes' && ! $product->isDigitalGoods()) {
                    $zip = str_pad($request->input('zip'), 5, '0', STR_PAD_LEFT);

                    $packstation = Packstation::query()
                        ->where('zip', 'LIKE', '%' . $zip . '%')
                        ->first();

                    if ($packstation instanceof Packstation) {
                        TidPackStation::query()
                            ->create([
                                'tid_id'            => $tid->id,
                                'packstation_id'    => $packstation->id,
                            ]);

                        // $extraCosts += 500; TODO: enable if after free period
                    }
                }
            } catch (Throwable $e) {}

            if ($isBoxingProduct) {
                if($product->category->slug=='morty-nachnahme' || $product->category->slug=='welt-nachnahme' || $product->category->slug=='lalo-nachnahme'){
                    $extraCosts += 3000; // 0 EUR
                }
                
                else{
                    $extraCosts += 2000; // 20 EUR
                }
                
                
            }

            if ( $amount > 0 && $product->isAvailableAmount($amount)) {

                if ( $product->isUnlimited() ) {
                    $amount = 1;
                }
                if($isBoxingProduct){
                    if($product->category->slug=='morty-nachnahme' || $product->category->slug=='welt-nachnahme'){
                        $otheramount =  0;
                    }
                    elseif($product->category->slug=='lalo-nachnahme'){
                        $otheramount =  $request->input('product_payment_amount', 500) * 7;
                    }
                    else{
                        $otheramount =  $request->input('product_payment_amount', 500) * 10;
                    }
                    
                }
                else{
                    $otheramount = $product->price_in_cent;
                }
                $priceInCent = $amount * $otheramount;
                $priceInCent += $extraCosts;
                
                if ( Auth::user()->balance_in_cent >= $priceInCent ) {

                    $newBalance = Auth::user()->balance_in_cent - $priceInCent;

                    Auth::user()->update( [
                        'balance_in_cent' => $newBalance
                    ] );

                    if ( $product->isUnlimited() || $product->isDigitalGoods()) {
                        $productItems = ProductItem::where('product_id', $product->id)->limit($amount ?? 5)->get();
                        $productItemsContent = implode('', $productItems->pluck(['content'])->toArray());
                        $orderDetails = [
                            'user_id'        => Auth::user()->id,
                            'product_id'     => $product->id,
                            'tid_id'         => (! $product->isDigitalGoods() && ! $isBoxingProduct) ? $tid->id : 0,
                            'name'           => $product->name,
                            'content'        => ! $product->isDigitalGoods() ? $product->content : $productItemsContent,
                            'price_in_cent'  => $product->price_in_cent,
                            'totalprice'     => $priceInCent,
                            // 'drop_info'      => $dropInfo,
                            'delivery_price' => $deliveryMethodPrice,
                            'delivery_name'  => $deliveryMethodName,
                            'status'         => $status,
                            'weight'         => ! $product->isDigitalGoods() ? 0 : $amount,
                            'weight_char'    => '',
                            'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
                            'tracking_number'   => $request->input('tracking_number'),
                            'qrcode'   => $request->input('qrcode'),
                        ];

                        if ($isBoxingProduct) {
                            $orderDetails['product_name'] = $request->input('product_name');
                            $orderDetails['product_size'] = $request->input('product_size');
                            $orderDetails['product_weight'] = $request->input('product_weight', 0);
                            $orderDetails['product_payment_amount'] = $request->input('product_payment_amount', 5000);
                            //$orderDetails['product_payment_amount'] = $priceInCent/10;
                            $orderDetails['product_package_labels_link'] = $request->input('product_package_labels_link');
                            $orderDetails['amazon_product_link'] = $request->input('amazon_product_link');
                            $orderDetails['total_price_in_btc'] = $this->bitcoinConverterService->toBitcoin(($priceInCent/100), Setting::getShopCurrency());
                        }

                        $order = UserOrder::create($orderDetails);

                        event(new OrderWasCreated($order->id));

                        if (! $product->isDigitalGoods() && ! $isBoxingProduct) {
                            $tid->update([ 'used' => 1 ]);
                        }

                        if (! $isBoxingProduct && ! $isRefundingProduct && ! $product->isDigitalGoods()) {
                            $this->saveShippingAddress( $order, $validated_address );
                        }

                        if (! $product->isDigitalGoods()) {
                            $product->update( [
                                'sells' => $product->sells + 1
                            ] );
                        } else {
                            foreach($productItems as $item) {
                                $item->delete();
                            }

                            $product->update( [
                                'sells'            => $product->sells + $amount
                            ] );
                        }

                        if ( $request->has( 'send_at' ) ) {
                            $order->update( [
                                'type_deliver' => 'desired_date',
                                'deliver_at'   => $request->input( 'send_at' )
                            ] );
                            $errorMessage =  '';
                        } else {
                            if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
                                $order->update( [
                                    'deliver_at' => Carbon::tomorrow()
                                ] );

                                $errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
                            } else {
                                $order->update( [
                                    'deliver_at' => Carbon::now()
                                ] );
                                $errorMessage =  '';
                            }
                        }

                        Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

                        if (! $isBoxingProduct && ! $isRefundingProduct && ! $product->isDigitalGoods()) {
                            $this->tidGenerationService->generateTidPDF($order->id);
                        }

                        Notification::create( [
                            'custom_id' => Auth::user()->id,
                            'type'      => 'order'
                        ] );


                        if($errorMessage != '') {
                                return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                    'successMessage' => __( 'frontend/shop.you_bought', [
                                        'name'  => $product->name,
                                        'price' => Product::formatPrice($priceInCent)
                                    ] )
                                ] )->with('errorMessage', $errorMessage);
                            }else {
                                return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                    'successMessage' => __( 'frontend/shop.you_bought', [
                                        'name'  => $product->name,
                                        'price' => Product::formatPrice($priceInCent)
                                    ] )
                                ] );

                            }

                    } else if ( $product->asWeight()) {

                        $order = UserOrder::create( [
                            'user_id'        => Auth::user()->id,
                            'product_id'     => $product->id,
                            'tid_id'         => ! $product->isDigitalGoods() ? $tid->id : 0,
                            'name'           => $product->name,
                            'content'        => $product->content,
                            'weight'         => $amount,
                            'weight_char'    => $product->getWeightChar(),
                            'price_in_cent'  => $product->price_in_cent,
                            'totalprice'     => $priceInCent,
                            // 'drop_info'      => $dropInfo,
                            'delivery_price' => $deliveryMethodPrice,
                            'delivery_name'  => $deliveryMethodName,
                            'status'         => $status,
                            'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
                            'tracking_number'   => $request->input('tracking_number')
                        ] );

                        event(new OrderWasCreated($order->id));

                        if (! $product->isDigitalGoods()) {
                            $tid->update( [ 'used' => 1 ] );
                        }

                        if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
                            $this->saveShippingAddress( $order, $validated_address );
                        }

                        $product->update( [
                            'sells'            => $product->sells + $amount,
                            'weight_available' => $product->weight_available - $amount
                        ] );

                        if ( $request->has( 'send_at' ) ) {
                            $order->update( [
                                'type_deliver' => 'desired_date',
                                'deliver_at' => $request->input( 'send_at' )
                            ] );
                            $errorMessage =  '';
                        } else {
                            if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
                                $order->update( [
                                        'deliver_at' => Carbon::tomorrow()
                                    // 'deliver_at' => date('Y-m-d', strtotime(Carbon::tomorrow()))
                                ] );

                                $errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
                            } else {
                                $order->update( [
                                        'deliver_at' => Carbon::now()
                                    // 'deliver_at' => date('Y-m-d', strtotime(Carbon::now()))
                                ] );
                                $errorMessage =  '';
                            }
                        }

                        Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

                        if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
                            $this->tidGenerationService->generateTidPDF($order->id);
                        }

                        Notification::create( [
                            'custom_id' => Auth::user()->id,
                            'type'      => 'order'
                        ] );

                        if($errorMessage != '') {
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount2', [
                                    'name'             => $product->name,
                                    'amount_with_char' => $amount . $product->getWeightChar(),
                                    'totalprice'       => Product::formatPrice( $priceInCent ),
                                    'price'            => $product->getFormattedPrice()
                                ] )
                            ] )->with('errorMessage', $errorMessage);
                        }else{
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount2', [
                                    'name'             => $product->name,
                                    'amount_with_char' => $amount . $product->getWeightChar(),
                                    'totalprice'       => Product::formatPrice( $priceInCent ),
                                    'price'            => $product->getFormattedPrice()
                                ] )
                            ] );
                        }

                    } else {
                        for ( $i = 0; $i < $amount; $i++ ) {

                            $productItem    = ProductItem::where( 'product_id', $product->id )->get()->first();
                            $productContent = $productItem->content;
                            $productItem->delete();

                            $order = UserOrder::create( [
                                'user_id'        => Auth::user()->id,
                                'product_id'     => $product->id,
                                'tid_id'         => ! $product->isDigitalGoods() ? $tid->id : 0,
                                'name'           => $product->name,
                                'content'        => $productContent,
                                'price_in_cent'  => $product->price_in_cent,
                                'totalprice'     => $priceInCent,
                                'weight'         => 0,
                                'weight_char'    => '',
                                'status'         => $status,
                                'delivery_price' => $deliveryMethodPrice,
                                'delivery_name'  => $deliveryMethodName,
                                // 'drop_info'      => $dropInfo,
                                'include_receipt'=> ($request->input('receipt') !== null && $request->input('receipt') === 'yes' ? 1 : 0),
                                'tracking_number'   => $request->input('tracking_number')
                            ] );

                            event(new OrderWasCreated($order->id));

                            if (! $product->isDigitalGoods()) {
                                $tid->update( [ 'used' => 1 ] );
                            }

                            if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
                                $this->saveShippingAddress( $order, $validated_address );
                            }

                            $product->update( [
                                'sells' => $product->sells + 1
                            ] );

                            if ( $request->has( 'send_at' ) ) {
                                $order->update( [
                                    'type_deliver' => 'desired_date',
                                    'deliver_at' => $request->input( 'send_at' )
                                ] );
                                $errorMessage =  '';
                            } else {
                                if ( Carbon::now() > Carbon::now()->setTime( 17, 30, 00 ) ) {
                                    $order->update( [
                                        'deliver_at' => Carbon::tomorrow()
                                    ] );

                                    $errorMessage = (! $product->isDigitalGoods()) ?  __( 'frontend/shop.delivery_tomorrow') : '';
                                } else {
                                    $order->update( [
                                        'deliver_at' => Carbon::now()
                                    ] );
                                    $errorMessage =  '';
                                }
                            }

                            Setting::set( 'shop.total_sells', Setting::get( 'shop.total_sells', 0 ) + 1 );

                            if (! $isRefundingProduct && ! $product->isDigitalGoods()) {
                                $this->tidGenerationService->generateTidPDF($order->id);
                            }

                            Notification::create( [
                                'custom_id' => Auth::user()->id,
                                'type'      => 'order'
                            ] );


                        }

                        if($errorMessage != '') {
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount', [
                                    'name'       => $product->name,
                                    'amount'     => $amount,
                                    'totalprice' => Product::formatPrice( $priceInCent ),
                                    'price'      => $product->getFormattedPrice()
                                ] )
                            ] )->with('errorMessage', $errorMessage);
                        }else {
                            return redirect()->route( 'orders-with-pageNumber', 1 )->with( [
                                'successMessage' => __( 'frontend/shop.you_bought_with_amount', [
                                    'name'       => $product->name,
                                    'amount'     => $amount,
                                    'totalprice' => Product::formatPrice( $priceInCent ),
                                    'price'      => $product->getFormattedPrice()
                                ] )
                            ] );
                        }
                    }

                    /*if(UserOrder::create([
                        'user_id' => Auth::user()->id,
                        'name' => $product->name,
                        'content' => $productContent,
                        'status' => $status,
                        'price_in_cent' => $product->price_in_cent,
                        'drop_info' => $dropInfo
                    ])) {
                        Setting::set('shop.total_sells', Setting::get('shop.total_sells', 0) + 1);

                        $product->update([
                            'sells' => $product->sells + 1
                        ]);

                        Notification::create([
                            'custom_id' => Auth::user()->id,
                            'type' => 'order'
                        ]);

                        return redirect()->route('orders-with-pageNumber', 1)->with([
                            'successMessage' => __('frontend/shop.you_bought', [
                                'name' => $product->name,
                                'price' => $product->getFormattedPrice()
                            ])
                        ]);
                    } else {
                        return redirect()->route('buy-product', [
                            'id' => $productId,
                            'amount' => $amount
                        ])->with([
                            'errorMessage' => __('frontend/shop.buy_error')
                        ]);
                    }*/

                } else {

                    return redirect()->route( 'buy-product', [
                        'id'     => $productId,
                        'amount' => $amount
                    ] )->with( [
                        'errorMessage' => __( 'frontend/shop.not_enought_money' )
                    ] );

                }
            } else {

                return redirect()->route( 'shop' )->with( [
                    'errorMessage' => __( 'frontend/shop.product_not_available' )
                ] );

            }

        }

        return redirect()->route( 'shop' );
    }


    /**
     * @param $order
     * @param $validated_address
     */
    public function saveShippingAddress( $order, $validated_address )
    {
        $validated_address = $validated_address->valid();
        $order->address()->create( $validated_address );
    }


    public function showProductPage( $productId )
    {
        $product = Product::with(['benifits'])->where('id', $productId)->first();

        if ( $product != null ) {
            return view( 'frontend/shop.products_category', [
                'products'        => [$product],
                'productCategory' => $product->getCategory() ?? (object)[ 'name' => __( 'frontend/shop.uncategorized' ) ]
            ] );
        }

        return view( 'frontend/shop.product_not_found' );
    }

    public function showProductCategoryPage( $slug = null )
    {
        if ( $slug == null && strtolower( $slug ) != 'uncategorized' ) {
            return redirect()->route( 'shop' );
        }

        $productCategory = ProductCategory::where( 'slug', $slug )->get()->first();

        if ( $productCategory == null && $slug != 'uncategorized' ) {
            return redirect()->route( 'shop' );
        } else if ( $productCategory == null ) {
            $products = Product::with(['benifits'])->getUncategorizedProducts();
        } else {
            $products = Product::with(['benifits'])->where( 'category_id', $productCategory->id )->get()->all();
        }
        
        return view( 'frontend/shop.products_category', [
            'products'        => $products,
            'productCategory' => $productCategory ?? (object)[ 'name' => __( 'frontend/shop.uncategorized' ) ]
        ] );
    }

    public function createTidFile( $order_id )
    {


        $order = UserOrder::find( $order_id );

        $original_name = $order->tids->tid;
        $file_loc = $order->tids->loc;

        $offset_x = 170;
        $offset_y = 30;
        if ( $file_loc == 'eu' ) {
            $offset_x = 35;
            $offset_y = 22;
        }

        $path = Storage::disk( 'public' )->path( "tid/$order->product_id/$original_name" );

        $pdf = new Fpdi();
        $pdf->setSourceFile( $path );

        $tplIdx = $pdf->importPage( 1 );
        $specs  = $pdf->getTemplateSize( $tplIdx );
        $pdf->AddPage( $specs[ 'height' ] > $specs[ 'width' ] ? 'P' : 'L' );
        $pdf->useTemplate( $tplIdx );

        $pdf->SetFont( 'arial', '', '10' );
        $pdf->SetTextColor( 0, 0, 0 );

        $order = UserOrder::find( $order_id );

        setlocale( LC_ALL, 'de_DE' );

        $shipping = Setting::where( 'key', 'like', 'shipping%' )->get();

        $settings = [];

        foreach ( $shipping->pluck( 'value', 'key' )->toArray() as $key => $setting )
            $settings[ explode( '.', $key )[ 1 ] ] = $setting;


        // sender_first_name sender_last_name
        $pdf->SetXY( $offset_x, $offset_y );
        $pdf->Write( 0, $this->codeToISO(
            $order->address->sender_first_name . ' ' . $order->address->sender_last_name
        ) );

        // sender_street
        $pdf->SetXY( $offset_x, $offset_y + 5 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_street ) );

        // sender_zip
        $pdf->SetXY( $offset_x, $offset_y + 10 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_zip ) );

        // sender_city
        $pdf->SetXY( $offset_x, $offset_y + 15 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_city ) );

        // sender_country
        $pdf->SetXY( $offset_x, $offset_y + 20 );
        $pdf->Write( 0, $this->codeToISO( $order->address->sender_country ) );


        // first_name last_name
        $pdf->SetXY( $offset_x, $offset_y + 30 );
        $pdf->Write( 0, $this->codeToISO( $order->address->first_name . ' ' . $order->address->last_name ) );

        // street
        $pdf->SetXY( $offset_x, $offset_y + 35 );
        $pdf->Write( 0, $this->codeToISO( $order->address->street ) );

        // zip
        $pdf->SetXY( $offset_x, $offset_y + 40 );
        $pdf->Write( 0, $this->codeToISO( $order->address->zip ) );

        // city
        $pdf->SetXY( $offset_x, $offset_y + 45 );
        $pdf->Write( 0, $this->codeToISO( $order->address->city ) );

        // country
        $pdf->SetXY( $offset_x, $offset_y + 50 );
        $pdf->Write( 0, $this->codeToISO( $order->address->country ) );


        $path = "order/$order_id";

        Storage::disk( 'public' )->makeDirectory( $path );

        $pdf->Output( public_path( "storage/order/$order_id/$original_name" ), 'F' );

        return back()->with( 'success', 'You have successfully upload file.' );
    }

    public function codeToISO( $str )
    {
        return iconv( 'UTF-8', 'ISO-8859-1', $str );
    }

    public function showdescription( $id )
    {
        $data = Product::where( 'id', $id )->get()->first();
        // print_r($data);exit;
        $name          = $data->name;
        $descrption    = $data->description;
        $decripteddesc = nl2br($descrption);
        return response()->json( [ 'name' => $name, 'descrption' => $decripteddesc ] );
        // print_r($data);
    }
}
