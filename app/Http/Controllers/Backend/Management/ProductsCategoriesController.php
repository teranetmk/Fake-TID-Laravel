<?php

    namespace App\Http\Controllers\Backend\Management;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use App\Models\ProductCategory;

use Validator;

    class ProductsCategoriesController extends Controller
    {
        public function __construct() {
            $this->middleware('backend');
			$this->middleware('permission:manage_products_categories');
        }

        public function deleteProductCategory($id) {
            ProductCategory::where('id', $id)->delete();

            return redirect()->route('backend-management-products-categories');
		}

		public function editProductCategoryForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('product_category_edit_id')) {
                    $productCategory = ProductCategory::where('id', $request->input('product_category_edit_id'))->get()->first();

                    if($productCategory != null) {
                        $validator = Validator::make($request->all(), [
                            'product_category_edit_name' => 'required|max:255',
                            'product_category_edit_slug' => 'required|unique:products_categories,slug,'.$request->input('product_category_edit_id'),
                            'product_category_edit_is_digital_goods' => 'nullableinteger'
                        ]);
        
                        if(!$validator->fails()) {
                            $name = $request->input('product_category_edit_name');
                            $slug = $request->input('product_category_edit_slug');
                            $isDigitalGoods = $request->input('product_category_edit_is_digital_goods', 0);
                            $ishow = $request->input('is_show');
                            $productCategory->update([
                                'name' => $name,
                                'slug' => $slug,
                                'is_show' =>$ishow,
                                'is_digital_goods' => (bool)$isDigitalGoods
                            ]);

                            return redirect()->route('backend-management-product-category-edit', $productCategory->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-product-category-edit', $productCategory->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-products-categories');
        }

        public function showProductCategoryEditPage($id) {
            $productCategory = ProductCategory::where('id', $id)->get()->first();

            if($productCategory == null) {
                return redirect()->route('backend-management-products-categories');
            }

            return view('backend.management.products.categories.edit', [
                'productCategory' => $productCategory,
                'managementPage' => true
            ]);
        }
		
		public function addProductCategoryForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'product_category_add_name' => 'required|max:255',
                    'product_category_add_slug' => 'required|unique:products_categories,slug|max:255',
                    // 'product_category_add_is_digital_goods' => 'nullableinteger'
                ]);

                if(!$validator->fails()) {
                    $name = $request->input('product_category_add_name');
                    $slug = $request->input('product_category_add_slug');
                    $ishow = $request->input('is_show');
                    $isDigitalGoods = $request->input('product_category_add_is_digital_goods', 0);
                   
                    ProductCategory::create([
                        'name' => $name,
                        'slug' => $slug,
                        'is_show' => $ishow,
                        'is_digital_goods' => (bool)$isDigitalGoods
                    ]);

                    return redirect()->route('backend-management-product-category-add')->with([
                        'successMessage' => __('backend/main.added_successfully')
                    ]);
                }

                $request->flash();
                return redirect()->route('backend-management-product-category-add')->withErrors($validator)->withInput();
            }

            return redirect()->route('backend-management-product-category-add');
        }

        public function showProductCategoryAddPage(Request $request) {
            return view('backend.management.products.categories.add', [
                'managementPage' => true
            ]);
        }

        public function showProductsCategoriesPage(Request $request, $pageNumber = 0) {
           
            $productsCategories = ProductCategory::orderByDesc('created_at')->paginate(10, ['*'], 'page', $pageNumber);
            
            if($pageNumber > $productsCategories->lastPage() || $pageNumber <= 0) {
                return redirect()->route('backend-management-products-categories-with-pageNumber', 1);
            }

            return view('backend.management.products.categories.list', [
                'productsCategories' => $productsCategories,
                'managementPage' => true
            ]);
        }
    }
