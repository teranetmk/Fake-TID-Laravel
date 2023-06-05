<?php

    namespace App\Http\Controllers\UserPanel;

    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    use App\Models\UserOrder;
    use App\Models\Coupon;
    use App\Models\UserTransaction;
    
    use App\Classes\BitcoinAPI;
use App\Filters\OrderFilters;
use App\Models\Product;
use App\Models\Product\ProductBenifit;
use App\Models\ProductItem;
use App\Models\Setting;
use App\Models\UserOrderNote;
use App\Rules\RuleCouponRedeem;
use App\Services\BtcpayApiService;
    use Validator;
    use Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\AnalyticsService;
use App\Http\Requests\AnalyticsRequest;
use App\Models\Bonus;
use App\Models\User;
use App\Models\UserTicket;
use App\Models\UserTicketReply;
use Illuminate\Support\Facades\Redirect;


    class UserPanelController extends Controller
    {
        /** @var AnalyticsService */
        private $analyticsService;
        private $btcpayApiService;
        private $exchangeRateService;
        public function __construct(AnalyticsService $analyticsService,BtcpayApiService $btcpayApiService) {
            $this->middleware('auth');
            $this->analyticsService = $analyticsService;
            $this->btcpayApiService = $btcpayApiService;
           
        }

        public function showUserDashboard()
        {
            
            return view('frontend/vendor.home');
        }

        public function showSettingsPage() {
            // if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
            //     return view('frontend/vendor.settings');
            // }
            // else{
            //     return view('frontend/userpanel.settings');
            // }
           
            return view('frontend/userpanel.settings');
          
        }
        
        public function redirectToSettingsPage() 
        {
            return redirect()->route('settings');
        }

        public function redeemCoupon(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'coupon_redeem_code' => new RuleCouponRedeem()
                ]);

                if(!$validator->fails()) {
                    $coupon = Coupon::where('code', $request->get('coupon_redeem_code'))->get()->first();

                    if($coupon != null) {
                        if(!Auth::user()->hasCouponUsed($coupon)) {
                            if(Auth::user()->redeemCoupon($coupon)) {
                                return redirect()->route('deposit')->with(['successMessage' => __('frontend/user.coupon_redeem.success', [
                                    'amount' => $coupon->getFormattedAmount(),
                                    'code' => $coupon->code
                                ])]);
                            }
                        } else {
                            $request->flash();
                            return redirect()->route('deposit')->withErrors(['coupon_redeem_code' => __('frontend/user.coupon_redeem.error3')])->withInput();
                        }
                    }
                } else {
                    $request->flash();
                    return redirect()->route('deposit')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('deposit');
        }

       public function btcRedeemCoupon(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'coupon_redeem_code' => new RuleCouponRedeem()
                ]);

                if(!$validator->fails()) {
                    $coupon = Coupon::where('code', $request->get('coupon_redeem_code'))->get()->first();

                    if($coupon != null) {
                        if(!Auth::user()->hasCouponUsed($coupon)) {
                            if(Auth::user()->redeemCoupon($coupon)) {
                                return redirect()->route('deposit-btc')->with(['successMessage' => __('frontend/user.coupon_redeem.success', [
                                    'amount' => $coupon->getFormattedAmount(),
                                    'code' => $coupon->code
                                ])]);
                            }
                        } else {
                            $request->flash();
                            return redirect()->route('deposit-btc')->withErrors(['coupon_redeem_code' => __('frontend/user.coupon_redeem.error3')])->withInput();
                        }
                    }
                } else {
                    $request->flash();
                    return redirect()->route('deposit-btc')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('deposit-btc');
        }

	public function btcRedeemPage(Request $request) {
            $userTransaction = UserTransaction::where('user_id', Auth::user()->id)->where('status', 'waiting')->orderByDesc('created_at')->get()->first();

            if($userTransaction == null) {
                $bitcoind = BitcoinAPI::getBitcoinClient();
                $btcWallet = $bitcoind->getnewaddress();

                $userTransaction = UserTransaction::create([
                    'user_id' => Auth::user()->id,
                    'wallet' => $btcWallet,
                    'status' => 'waiting',
                    'amount' => 0,
                    'amount_cent' => 0,
                    'txid' => ''
                ]);
            } else {
                $btcWallet = $userTransaction->wallet;
            }
            // if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
            //     return view('frontend/vendor.deposit_btc_redeem', [
            //         'btcWallet' => $btcWallet,
            //         'clipboardJS' => (object) [
            //             'element' => '.btc-cashin-copy-btn',
            //             'fadeIn' => '.btc-cashin-copy-info'
            //         ],
            //         'userTransactionID' => $userTransaction->id
            //     ]);

            // }
            // else{
            //     return view('frontend/userpanel.deposit_btc_redeem', [
            //         'btcWallet' => $btcWallet,
            //         'clipboardJS' => (object) [
            //             'element' => '.btc-cashin-copy-btn',
            //             'fadeIn' => '.btc-cashin-copy-info'
            //         ],
            //         'userTransactionID' => $userTransaction->id
            //     ]);

            // }
            return view('frontend/userpanel.deposit_btc_redeem', [
                'btcWallet' => $btcWallet,
                'clipboardJS' => (object) [
                    'element' => '.btc-cashin-copy-btn',
                    'fadeIn' => '.btc-cashin-copy-info'
                ],
                'userTransactionID' => $userTransaction->id
            ]);
            
        }

        public function passwordChangeForm(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'settings_current_password' => [
                        'required'
                    ],
                    'settings_new_password' => [
                        'required', 'min:6', 'max:255', 'same:settings_new_password_confirm'
                    ],
                    'settings_new_password_confirm' => [
                        'required', 'min:6', 'max:255'
                    ],
                ]);

                if(!$validator->fails()) {
                    if(Hash::check($request->input('settings_current_password'), Auth::user()->password)) {
                        Auth::user()->update([
                            'password' => bcrypt($request->input('settings_new_password_confirm'))
                        ]);
                        
                        return redirect()->route('settings')->with('successMessageSettingsPassword', __('frontend/user.success_password_changed'));
                    } else {
                        $validator->getMessageBag()->add('settings_current_password', __('frontend/user.settings_current_password_wrong'));

                        $request->flash();
                        return redirect()->route('settings')->withErrors($validator)->withInput();
                    }
                } else {
                    $request->flash();
                    return redirect()->route('settings')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('settings');
        }

        public function jabberIDChangeForm(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'settings_jabber_id' => [
                        'required', 'string', 'email', 'max:255', 'unique:users,jabber_id,' . Auth::user()->id
                    ]
                ]);

                if(!$validator->fails()) {
                    $jabberID = $request->input('settings_jabber_id');

                    Auth::user()->update([
                        'jabber_id' => $jabberID,
                        'newsletter_enabled' => $request->get('newsletter_enabled') ? 1 : 0
                    ]);
                } else {
                    $request->flash();
                    return redirect()->route('settings')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('settings');
        }

        public function mailAddressChangeForm(Request $request) {
            if($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'settings_mail_address' => [
                        'required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::user()->id
                    ],
                ]);

                if(!$validator->fails()) {
                    $mailAddress = $request->input('settings_mail_address');

                    Auth::user()->update([
                        'email' => $mailAddress
                    ]);
                } else {
                    $request->flash();
                    return redirect()->route('settings')->withErrors($validator)->withInput();
                }
            }

            return redirect()->route('settings');
        }

       
		
		
		public function depositBtcPaidCheck($userTransactionID) {
           
            $userTransaction = UserTransaction::where('txid', $userTransactionID)->where('user_id', Auth::user()->id)->orderByDesc('created_at')->get()->first();
            
            if($userTransaction == null) 
			{
				
				//return $userTransaction;
               return redirect()->route('deposit');
			   
            } else {
                
                
                
				if($userTransaction->updateWhenPaidBtc()){
                    return redirect()->route('transactions');
                }
                else{
                    return redirect()->route('transactions');
                }
				
				
               
            }
        }

        /*public function depositBtcPaidCheck(Request $request) {
			$userTransactionID=$request->userTransactionID;
			//dd($userTransactionID);
            $userTransaction = UserTransaction::where('id', $userTransactionID)->where('user_id', Auth::user()->id)->first();
            
			
            if($userTransaction == null) {
				
                return 'null';//redirect()->route('deposit');
            } else {
                // if (! $userTransaction->updateWhenPaidBtc()) {
                //     return redirect()->route('btc-connection');  
                // }
				//dd($userTransaction->updateWhenPaidBtc());
				//dd($userTransaction->updateWhenPaidBtc());
                if($userTransaction->updateWhenPaidBtc()==false)
				{
					return 'false';
				}else{
					return 'true';
				}

                //return redirect()->route('transactions');
            }
        }*/

        public function showDepositBtcPage() {
            
            return view('frontend/userpanel.deposit_btc');  
            
        }

        public function createBTCInvoice(Request $request){
           
           
            $orderId = strtotime(date('m-d-y h:i'));
            $data = array('metadata'=>array('orderId'=>$orderId),'amount'=>$request->euro_amount,'checkout'=>array('redirectURL'=>'https://fake-tids.to/meine-einzahlungen'));
            $result = $this->btcpayApiService->createInvoice($data);
         
            if($result!=''){
                $amountCent = intval(BitcoinAPI::convertBtc($request->btc_amount) * floatval(Setting::get('shop.bonus_in_percent', "1")));
                $bonuses = Bonus::orderByDESC('min_amount')->get();
                $walletinfo = $this->btcpayApiService->getWalletAddress();
                foreach($bonuses as $bonus) {
                    if($amountCent >= $bonus->min_amount) {
                        $amountCent = $amountCent * floatval($bonus->percent);
                        break;
                    }
                }
                $transaction = UserTransaction::create([
                    'user_id' => Auth::user()->id,
                    'wallet' => $walletinfo['address'],
                    'txid' => $result['id'],
                    'status' => 'pending',
                    'amount' => intval($request->btc_amount*100000),
                    'amount_cent' => intval($amountCent),
                ]);
               
                return Redirect::to($result['checkoutLink']);

            }
            else{
                return redirect()->route('btc-einzahlung')->with('successMessageSettingsPassword', 'There is an error. Please try again');
            }
        }

        public function showOrdersPage(Request $request)
        {
            //dd(UserOrder::where( 'user_id', Auth::user()->id )->get()->toArray());
            $user_orders = UserOrder::where( 'user_id', Auth::user()->id )->with([
                'products',
                'products.category',
                'address',
                'tids',
                'tids.packStation'
            ])->orderByDesc( 'id' )->paginate(5)->setPath(route('orders'));

            $shopCurrency = \App\Models\Setting::getShopCurrency();
			//
            /*if ( $pageNumber > $user_orders->lastPage() || $pageNumber <= 0 ) {
                return redirect()->route( 'orders-with-pageNumber', 1 );
            }
			*/
			
            // if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                
            //     return view( 'frontend/vendor.orders', [
            //         'user_orders' => $user_orders
            //     ] );
            // }
            // else{
               
            //     return view( 'frontend/userpanel.orders', [
            //         'user_orders' => $user_orders
            //     ] );
            // }
            return view( 'frontend/userpanel.orders', [
                'user_orders' => $user_orders,
                'shop_currency' => $shopCurrency
            ] );
            
        }

        public function showTransactionsPage($pageNumber = 0) {
            
            
            $user_transactions = UserTransaction::where('user_id', Auth::user()->id)
                        ->orderByDesc('created_at')->paginate(5, ['*'], 'page', $pageNumber);


			//dd($user_transactions);

            

            if($pageNumber > $user_transactions->lastPage() || $pageNumber <= 0) {
                return redirect()->route('transactions-with-pageNumber', 1);
            }

       
            // if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
            //     return view('frontend/vendor.transactions', [
            //         'user_transactions' => $user_transactions
            //     ]);
            // }
            // else{
            //     return view('frontend/userpanel.transactions', [
            //         'user_transactions' => $user_transactions
            //     ]);
            // }
            return view('frontend/userpanel.transactions', [
                'user_transactions' => $user_transactions
            ]);
           
        }

        public function ShowMyOrders($pageNumber = 0, Request $request, OrderFilters $filters){
            $term = $request->query('term');
            $ordersAccounts = UserOrder::filter($filters)
            ->whereHas('products', function ($product) {
                return $product->whereHas('category', function ($category) {
                    $category->where('slug', 'accounts');
                });
            })
            ->orderByDesc('id')
            ->paginate(25, ['*'],'page', $pageNumber)->setPath(route('my-orders'));

            return view('frontend/vendor.myorderlist',compact('ordersAccounts','term'));
        }

        public function showOrderDetail($id){
            $order = UserOrder::with(['products'])->where('id', $id)->first();
        if (! $order instanceof UserOrder) {
            return redirect()->route('not-found');
        }

        $notes   = UserOrderNote::orderByDesc( 'created_at' )->where( 'order_id', $id )->get();
        $product = $order->products;
        $isBoxingProduct = in_array(strtolower($product->name), ['nachnahme boxing']);

        $shipping = Setting::where( 'key', 'like', 'shipping%' )->get();

        $settings = [];

        foreach ( $shipping->pluck( 'value', 'key' )->toArray() as $key => $setting )
            $settings[ explode( '.', $key )[ 1 ] ] = $setting;

        $settings = (object)$settings;

        if ( $order != null ) {
            if(Auth::user()->is_partner){
                return view( 'frontend.partner.myrodershow', compact( 'order', 'notes', 'product', 'settings', 'isBoxingProduct' ) );
            }
           
                return view( 'frontend.vendor.myrodershow', compact( 'order', 'notes', 'product', 'settings', 'isBoxingProduct' ) );
            }
        }
        public function PartnerOrdersetStatus( $id, Request $request )
        {
            UserOrder::find( $id )->update( [ 'status' => $request->status ] );
    
            return back();
        }

        public function showPartnerProducts(Request $request)
        {
           
            $products = Product::whereIn('id',[53,54,55])->orderByDesc('created_at')
                ->paginate(10, [ '*' ], 'page', $request->query('page', 1))->setPath(route('partner-management-products'));
         
            return view('frontend.partner.productlist', [
                'products'       => $products,
                'managementPage' => true
            ]);
            
            
           
        }
        public function showProductEditPage(Request $request, $id) {
            $product = Product::where('id', $id)->get()->first();
            $benifits = ProductBenifit::query()
                ->select(ProductBenifit::LABEL_COLUMN)
                ->where(ProductBenifit::PRODUCT_ID_COLUMN, $product->id)
                ->get();

            $benifitsInline = "";

            foreach ($benifits as $benifit) {
                $benifitsInline .= $benifit->getLabel() . PHP_EOL;
            }
           
                return view('frontend.partner.productedit', [
                    'product'           => $product,
                    'benifits'          => $benifitsInline,
                    'managementPage'    => true
                ]);
            
        }

        public function editProductForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('product_edit_id')) {
                    $product = Product::where('id', $request->input('product_edit_id'))->get()->first();

                    if($product != null) {
                        $rules = [
                            'product_edit_name' => 'required|max:255',
                            'product_edit_description' => 'required|max:5000',
                            'product_edit_short_description' => 'required|max:255',
                            'product_edit_content' => 'max:2000',
                            'product_edit_price_in_cent' => 'required|integer',
                            'product_edit_old_price_in_cent' => 'nullable|integer',
                            'product_edit_stock_management'=> 'required|in:normal,weight,unlimited',
                            'product_edit_benifits' => 'nullable|string',
                            'product_edit_show_stock' => 'nullable|integer',
                            'product_edit_delete_icon' => 'nullable|integer',
                            'product_edit_icon' => 'nullable|mimes:jpg,png,jpeg,gif,svg,webp|max:2000',
                        ];

                        if ($product->isDigitalGoods()) {
                            $rules['product_edit_order_minimum'] = 'nullable|integer';
                        }

                        $validator = Validator::make($request->all(), $rules);

                        if(!$validator->fails()) {
                            $name = $request->input('product_edit_name');
                            $description = $request->input('product_edit_description');
                            $short_description = $request->input('product_edit_short_description');
                            $content = $request->get('product_edit_content') ? $request->input('product_edit_content') : '';
                            $price_in_cent = $request->input('product_edit_price_in_cent');
                            $old_price_in_cent = $request->input('product_edit_old_price_in_cent') ?? 0;
                            $category_id = $request->input('product_edit_category_id');
                            $product_edit_stock_management = $request->input('product_edit_stock_management');
                            $isShowStockEnabled = (bool)$request->input('product_edit_show_stock', 0);
                            $orderMinimum = $request->input('product_edit_order_minimum', 1);

                            $as_weight = 0;
                            $weight_available = 0;
                            $stock_management = 1;
                            $weightchar = '';

                            if($product_edit_stock_management == 'unlimited') {
                                $stock_management = 0;
                            } else if($product_edit_stock_management == 'weight') {
                                $stock_management = 0;
                                $as_weight = 1;

                                if($request->get('product_edit_weight')) {
                                    $weight_available = intval($request->get('product_edit_weight'));
                                }

                                if($request->get('product_edit_weightchar')) {
                                    $weightchar = $request->get('product_edit_weightchar');
                                } else {
                                    $weightchar = 'g';
                                }
                            }

                            $drop_needed = 0;
                            if($request->get('product_edit_drop_needed')) {
                                $drop_needed = 1;
                            }

                            $data = [
                                'name' => $name,
                                'description' => $description,
                                'short_description' => $short_description,
                                'price_in_cent' => $price_in_cent,
                                'old_price_in_cent' => $old_price_in_cent,
                                'drop_needed' => $drop_needed,
                                'category_id' => $category_id,
                                'stock_management' => $stock_management,
                                'as_weight' => $as_weight,
                                'weight_available' => $weight_available,
                                'weight_char' => $weightchar,
                                'content' => $content,
                                'show_stock' => $isShowStockEnabled
                            ];

                            if (! $request->hasFile('product_edit_icon') && $request->input('product_edit_delete_icon') == 1) {
                                $data['icon'] = null;
                            }

                            if ($request->hasFile('product_edit_icon')) {
                                $name = time() . '.' .  $request->file('product_edit_icon')->getClientOriginalName();
                                $request->file('product_edit_icon')->move(public_path('icons'), $name);

                                $data['icon'] = $name;
                            }

                            if ($product->isDigitalGoods()) {
                                $data['order_minimum'] = $orderMinimum;
                            }

                            $oldIcon = $product->getIcon();
                            $product->update($data);

                            if (! is_null($oldIcon)) {
                                Storage::delete(public_path('icons/' . $oldIcon));
                            }

                            $benifits = explode(PHP_EOL, $request->input('product_edit_benifits') ?? '');

                            ProductBenifit::query()->where(ProductBenifit::PRODUCT_ID_COLUMN, $product->id)->delete();

                            if (count($benifits) > 0) {
                                foreach ($benifits as $benifit) {
                                    ProductBenifit::create([
                                        ProductBenifit::LABEL_COLUMN => $benifit,
                                        ProductBenifit::PRODUCT_ID_COLUMN => $product->id
                                    ]);
                                }
                            }

                            return redirect()->route('partner-product-edit', $product->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('partner-product-edit', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('partner-management-products');
        }

        public function showProductDatabasePage($id) {
            $product = Product::where('id', $id)->get()->first();

            if($product == null || $product->isUnlimited()) {
                return redirect()->route('partner-management-products');
            }

            $items = ProductItem::where('product_id', $product->id)->get();
            
                return view('frontend.partner.database', [
                    'product' => $product,
                    'managementPage' => true,
                    'items' => $items
                ]);
            
        }

        public function databaseImportTXT(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('product_id')) {
                    $product = Product::where('id', $request->input('product_id'))->get()->first();

                    if($product != null && !$product->isUnlimited() && !$product->asWeight()) {
                        $validator = Validator::make($request->all(), [
                            'import_txt_input' => 'required|max:10000',
                            'product_import_txt_option' => 'required|IN:seperator,linebyline'
                        ]);

                        if(!$validator->fails()) {
                            $input = $request->input('import_txt_input');
                            $type = $request->input('product_import_txt_option');

                            $count = 0;

                            $seperator = "\n";
                            if($type == 'seperator') {
                                if(!$request->get('product_import_txt_seperator_input')) {
                                    $validator->getMessageBag()->add('product_import_txt_seperator_input', __('backend/management.products.database.import.txt.seperator_required'));

                                    $request->flash();
                                    return redirect()->route('partner-product-database', $product->id)->withErrors($validator)->withInput();
                                }

                                $seperator = $request->input('product_import_txt_seperator_input');

                                Setting::set('import.custom.delimiter', $seperator);
                            }

                            $items = explode($seperator, trim($input));
                            $items = array_filter($items, 'trim');

                            foreach($items as $line) {
                                if(strlen($line) <= 0) {
                                    continue;
                                }

                                if(ProductItem::create([
                                    'product_id' => $product->id,
                                    'content' => $line
                                ])) {
                                    $count++;
                                }
                            }

                            return redirect()->route('partner-product-database', $product->id)->with([
                                'successMessage' => __('backend/management.products.database.import.successfully', [
                                    'count' => $count
                                ])
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('partner-product-database', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('partner-product-database');
        }

        public function databaseImportONE(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('product_id')) {
                    $product = Product::where('id', $request->input('product_id'))->get()->first();

                    if($product != null && !$product->isUnlimited()) {
                        $validator = Validator::make($request->all(), [
                            'import_one_content' => 'required|max:1000'
                        ]);

                        if(!$validator->fails()) {
                            $content = $request->input('import_one_content');

                            ProductItem::create([
                                'product_id' => $product->id,
                                'content' => $content
                            ]);

                            return redirect()->route('partner-product-database', $product->id)->with([
                                'successMessage' => __('backend/management.products.database.import.one_successfully')
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('partner-product-database', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('partner-product-database');
        }
        
        public function databaseImportItems(Request $request) {
            if ($request->getMethod() == 'POST') {
                if(!is_null($request->get('product-id'))) {
                    $product = Product::where('id', $request->input('product-id'))->first();

                    if($product instanceof Product) {
                        $validator = Validator::make($request->all(), [
                            'product-items' => 'required|string'
                        ]);

                        if(! $validator->fails()) {
                            $items = explode(PHP_EOL, $request->input('product-items', ''));

                            try {
                                DB::beginTransaction();

                                if (count($items) > 0) {
                                    ProductItem::where('product_id', $product->id)->delete();
                                }
    
                                foreach($items as $item) {
                                    if (strlen($item) === 0) {
                                        continue;
                                    }
    
                                    ProductItem::create([
                                        'product_id'    => $product->id,
                                        'content'       => $item
                                    ]);
                                }

                                DB::commit();
                            } catch (Throwable $e) {
                                DB::rollBack();
                            }

                            return redirect()->route('partner-product-database', $product->id)->with([
                                'successMessage' => __('backend/management.products.database.import.one_successfully')
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('partner-product-database', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('partner-management-products');
        }

        public function ShowPartnerOrders($pageNumber = 0, Request $request, OrderFilters $filters){
            $term = $request->query('term');
            $ordersAccounts = UserOrder::whereIn('product_id',[53,54,55])
            ->orderByDesc('id')
            ->paginate(25, ['*'],'page', $pageNumber)->setPath(route('partner-orders'));

            return view('frontend/partner.myorderlist',compact('ordersAccounts','term'));
        }

        public function partnerProfit(AnalyticsRequest $request){
            $currentMonth = $this->analyticsService->getCurrentMonthPeriod();
            $currentYear = $this->analyticsService->getCurrentYearPeriod();
    
            $startDate = $request->input('start-date', $currentMonth->getStartDate()->format('Y/m/d'));
            $endDate = $request->input('end-date', $currentMonth->getEndDate()->format('Y/m/d'));
            //$startDate = '2022-06-01';
            //$endDate = '2022-06-30';
    
            $period = $this->analyticsService->getPeriod(Carbon::parse($startDate . ' 00:00:00'), Carbon::parse($endDate . ' 23:59:59'));

            return view(
                'frontend.partner.profit',
                [
                    'currentMonth'          => $currentMonth,
                    'startDate'             => $startDate,
                    'endDate'               => $endDate,
                    'yearStartDate'         => $currentYear->getStartDate(),
                    'yearEndDate'           => $currentYear->getEndDate(),
                    'accountsSalesChart'    => $this->analyticsService->getAccountsSalesChart($period),
                    'tidsSalesChart'        => $this->analyticsService->getTidsSalesChart($period),
                    'yearAccountsSalesChart'=> $this->analyticsService->getAccountsSalesChart($currentYear, true),
                    'yearTidsSalesChart'    => $this->analyticsService->getTidsSalesChart($currentYear, true),
                    'todaySales'            => $this->analyticsService->getTodaySales(),
                    'yesterdaySales'        => $this->analyticsService->getYesterdaySales(),
                    'currentWeekSales'      => $this->analyticsService->getCurrentWeekSales(),
                    'currentMonthSales'     => $this->analyticsService->getCurrentMonthSales(),
                    'currentYearSales'      => $this->analyticsService->getCurrentYearSales(),
                ]
            );

        }

        public function PartnerTickets(Request $request) {
            $tickets = UserTicket::orderByRaw("CASE status
            WHEN 'open' THEN 1
            WHEN 'answered' THEN 2
            WHEN 'closed' THEN 3
            ELSE 4
        END")->where('category_id',5)->paginate(10)->setPath(route('partner-tickets'));
           
            
                return view('frontend.partner.ticketlist', [
                    'tickets' => $tickets,
                    'managementPage' => true
                ]);
            
        }

        public function partnerTicketEditPage($id) {
         
            $ticket = UserTicket::where('id', $id)->get()->first();

            if($ticket == null) {
                return redirect()->route('partner-tickets');
            }

			$ticketReplies = UserTicketReply::where('ticket_id', $ticket->id)->get()->all();
           
                return view('frontend.partner.ticketedit', [
                    'ticket' => $ticket,
                    'ticketReplies' => $ticketReplies,
                    'managementPage' => true
                ]);

            
            
        }

        public function partnerreplyTicketForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('ticket_reply_id')) {
                    $ticket = UserTicket::where('id', $request->input('ticket_reply_id'))->get()->first();

                    if($ticket != null) {
                        $validator = Validator::make($request->all(), [
                            'ticket_reply_msg' => 'required|max:5000'
                        ]);
        
                        if(!$validator->fails()) {
                            $message = $request->input('ticket_reply_msg');
            
                            UserTicketReply::create([
                                'ticket_id' => $ticket->id,
                                'user_id' => Auth::user()->id,
                                'content' => $message
                            ]);

                            $ticket->update([
                                'status'    => 'answered'
                            ]);

                            return redirect()->route('partner-ticket-edit', $ticket->id);
                        }
        
                        $request->flash();
                        return redirect()->route('partner-ticket-edit', $ticket->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('partner-tickets');
        }

        public function partnerdeleteTicket($id) {
            UserTicket::where('id', $id)->delete();

            return redirect()->route('partner-tickets');
        }

        public function PartnercloseTicket($id) {
            $ticket = UserTicket::where('id', $id)->get()->first();

			if($ticket != null) {
				$ticket->update([
					'status' => 'closed'
				]);

				return redirect()->route('partner-ticket-edit', $ticket->id);
			}

            return redirect()->route('partner-tickets');
        }

        public function PartneropenTicket($id) {
            $ticket = UserTicket::where('id', $id)->get()->first();

			if($ticket != null) {
				$ticket->update([
					'status' => 'open'
				]);

				return redirect()->route('partner-ticket-edit', $ticket->id);
			}

            return redirect()->route('partner-tickets');
        }
    }
