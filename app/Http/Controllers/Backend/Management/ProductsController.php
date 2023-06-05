<?php

    namespace App\Http\Controllers\Backend\Management;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use App\Models\Product;
    use App\Models\Product\ProductBenifit;
    use App\Models\ProductItem;
    use App\Models\Setting;

    use App\Rules\RuleProductCategoryExists;
use Throwable;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Auth;

    class ProductsController extends Controller
    {
        public function __construct() {
            $this->middleware('backend');
			$this->middleware('permission:manage_products');
        }

        public function deleteProduct($id) {
            Product::where('id', $id)->delete();
            ProductItem::where('product_id', $id)->delete();

            return redirect()->route('backend-management-products');
        }

        public function showProductDatabasePage($id) {
            $product = Product::where('id', $id)->get()->first();

            if($product == null || $product->isUnlimited()) {
                return redirect()->route('backend-management-products');
            }

            $items = ProductItem::where('product_id', $product->id)->get();
            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                return view('frontend.vendor.database', [
                    'product' => $product,
                    'managementPage' => true,
                    'items' => $items
                ]);
            }
            else{
                return view('backend.management.products.database', [
                    'product' => $product,
                    'managementPage' => true,
                    'items' => $items
                ]);

            }
            
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
                                    return redirect()->route('backend-management-product-database', $product->id)->withErrors($validator)->withInput();
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

                            return redirect()->route('backend-management-product-database', $product->id)->with([
                                'successMessage' => __('backend/management.products.database.import.successfully', [
                                    'count' => $count
                                ])
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('backend-management-product-database', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-products');
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

                            return redirect()->route('backend-management-product-database', $product->id)->with([
                                'successMessage' => __('backend/management.products.database.import.one_successfully')
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('backend-management-product-database', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-products');
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

                            return redirect()->route('backend-management-product-database', $product->id)->with([
                                'successMessage' => __('backend/management.products.database.import.one_successfully')
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('backend-management-product-database', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-products');
        }

        public function editProductForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('product_edit_id')) {
                    $product = Product::where('id', $request->input('product_edit_id'))->get()->first();

                    if($product != null) {
                        $rules = [
                            'product_edit_name' => 'required|max:255',
                            'product_edit_description' => 'required|max:5000',
                            'product_edit_category_id' => new RuleProductCategoryExists(),
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

                            return redirect()->route('backend-management-product-edit', $product->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }

                        $request->flash();
                        return redirect()->route('backend-management-product-edit', $product->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-products');
        }

        public function showProductEditPage(Request $request, $id) {
            $product = Product::where('id', $id)->get()->first();

            if($product == null) {
                return redirect()->route('backend-management-products');
            }

            $benifits = ProductBenifit::query()
                ->select(ProductBenifit::LABEL_COLUMN)
                ->where(ProductBenifit::PRODUCT_ID_COLUMN, $product->id)
                ->get();

            $benifitsInline = "";

            foreach ($benifits as $benifit) {
                $benifitsInline .= $benifit->getLabel() . PHP_EOL;
            }
            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                return view('frontend.vendor.productedit', [
                    'product'           => $product,
                    'benifits'          => $benifitsInline,
                    'managementPage'    => true
                ]);
            }
            else{
                return view('backend.management.products.edit', [
                    'product'           => $product,
                    'benifits'          => $benifitsInline,
                    'managementPage'    => true
                ]);
            }
           
        }

        public function addProductForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'product_add_name' => 'required|max:255',
                    'product_add_description' => 'required|max:5000',
                    'product_add_category_id' => new RuleProductCategoryExists(),
                    'product_add_short_description' => 'required|max:255',
                    'product_add_content' => 'max:2000',
                    'product_add_price_in_cent' => 'required|integer',
                    'product_add_old_price_in_cent' => 'nullable|integer',
                    'product_add_stock_management'=> 'required|in:normal,weight,unlimited'
                ]);

                if(!$validator->fails()) {
                    $name = $request->input('product_add_name');
                    $description = $request->input('product_add_description');
                    $short_description = $request->input('product_add_short_description');
                    $content = $request->get('product_add_content') ? $request->input('product_add_content') : '';
                    $price_in_cent = $request->input('product_add_price_in_cent');
                    $old_price_in_cent = $request->input('product_add_old_price_in_cent') ?? 0;
                    $category_id = $request->input('product_add_category_id');
                    $product_add_stock_management = $request->input('product_add_stock_management');

                    $as_weight = 0;
                    $weight_available = 0;
                    $stock_management = 1;
                    $weightchar = '';

                    if($product_add_stock_management == 'unlimited') {
                        $stock_management = 0;
                    } else if($product_add_stock_management == 'weight') {
                        $stock_management = 0;
                        $as_weight = 1;

                        if($request->get('product_add_weight')) {
                            $weight_available = intval($request->get('product_add_weight'));
                        }

                        if($request->get('product_add_weightchar')) {
                            $weightchar = $request->get('product_add_weightchar');
                        } else {
                            $weightchar = 'g';
                        }
                    }

                    $drop_needed = 0;
                    if($request->get('product_add_drop_needed')) {
                        $drop_needed = 1;
                    }

                    Product::create([
                        'name' => $name,
                        'description' => $description,
                        'short_description' => $short_description,
                        'price_in_cent' => $price_in_cent,
                        'old_price_in_cent' => $old_price_in_cent,
                        'category_id' => $category_id,
                        'drop_needed' => $drop_needed,
                        'stock_management' => $stock_management,
                        'as_weight' => $as_weight,
                        'weight_available' => $weight_available,
                        'weight_char' => $weightchar,
                        'content' => $content,
                        'sells' => 0
                    ]);

                    return redirect()->route('backend-management-product-add')->with([
                        'successMessage' => __('backend/main.added_successfully')
                    ]);
                }

                $request->flash();
                return redirect()->route('backend-management-product-add')->withErrors($validator)->withInput();
            }

            return redirect()->route('backend-management-product-add');
        }

        public function showProductAddPage(Request $request) {
            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                return view('frontend.vendor.productadd', [
                    'managementPage' => true
                ]);
            }
            else{
                return view('backend.management.products.add', [
                    'managementPage' => true
                ]);
            }
            
        }

        public function showProductsPage(Request $request)
        {
            $products = Product::withCount(['tids' => function ($q) {
                $q->where('used', 0);
            }])->with(['tids']);
            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                $products->where('category_id',10);
            }
            
            $products = $products->orderByDesc('created_at')
                ->paginate(10, [ '*' ], 'page', $request->query('page', 1))->setPath(route('backend-management-products'));
            if(!Auth::user()->isSuperAdmin() && Auth::user()->hasPermission('vendor')){
                return view('frontend.vendor.productlist', [
                    'products'       => $products,
                    'managementPage' => true
                ]);
            }
            else{
                return view('backend.management.products.list', [
                    'products'       => $products,
                    'managementPage' => true
                ]);
            }
           
        }

        public static function getSqlWithBindings( $query )
        {
            return vsprintf( str_replace( '?', '%s', $query->toSql() ), collect( $query->getBindings() )->map( function ( $binding ) {
                return is_numeric( $binding ) ? $binding : "'{$binding}'";
            } )->toArray() );
        }

    }
