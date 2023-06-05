<?php

    namespace App\Http\Controllers\Backend\Management;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use App\Models\DeliveryMethod;
    
    use Auth;
    use Validator;

    class DeliveryMethodsController extends Controller
    {
        public function __construct() {
            $this->middleware('backend');
			$this->middleware('permission:manage_delivery_methods');
        }

        public function deleteDeliveryMethod($id) {
            DeliveryMethod::where('id', $id)->delete();

            return redirect()->route('backend-management-delivery-methods');
        }

        public function editDeliveryMethodForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('delivery_method_edit_id')) {
                    $deliveryMethod = DeliveryMethod::where('id', $request->input('delivery_method_edit_id'))->get()->first();

                    if($deliveryMethod != null) {
                        $validator = Validator::make($request->all(), [
                            'delivery_method_edit_name' => 'required|max:255',
                            'delivery_method_edit_price' => 'required|integer'
                        ]);
        
                        if(!$validator->fails()) {
                            $name = $request->input('delivery_method_edit_name');
                            $price = $request->input('delivery_method_edit_price');
            
                            $deliveryMethod->update([
                                'name' => $name,
                                'price' => $price
                            ]);

                            return redirect()->route('backend-management-delivery-method-edit', $deliveryMethod->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-delivery-method-edit', $deliveryMethod->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-delivery-methods');
        }

        public function showDeliveryMethodEditPage($id) {
            $deliveryMethod = DeliveryMethod::where('id', $id)->get()->first();

            if($deliveryMethod == null) {
                return redirect()->route('backend-management-delivery-methods');
            }

            return view('backend.management.delivery_methods.edit', [
                'deliveryMethod' => $deliveryMethod,
                'managementPage' => true
            ]);
        }

        public function addDeliveryMethodForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'delivery_method_add_name' => 'required|max:255',
                    'delivery_method_add_price' => 'required|integer'
                ]);

                if(!$validator->fails()) {
                    $name = $request->input('delivery_method_add_name');
                    $price = $request->input('delivery_method_add_price');
                    
                    DeliveryMethod::create([
                        'name' => $name,
                        'price' => $price
                    ]);

                    return redirect()->route('backend-management-delivery-method-add')->with([
                        'successMessage' => __('backend/main.added_successfully')
                    ]);
                }

                $request->flash();
                return redirect()->route('backend-management-delivery-method-add')->withErrors($validator)->withInput();
            }

            return redirect()->route('backend-management-delivery-method-add');
        }

        public function showDeliveryMethodAddPage(Request $request) {
            return view('backend.management.delivery_methods.add', [
                'managementPage' => true
            ]);
        }

        public function showDeliveryMethodsPage(Request $request, $pageNumber = 0) {
            $deliveryMethods = DeliveryMethod::orderByDesc('created_at')->paginate(10, ['*'], 'page', $pageNumber);
            
            if($pageNumber > $deliveryMethods->lastPage() || $pageNumber <= 0) {
                return redirect()->route('backend-management-delivery-methods-with-pageNumber', 1);
            }

            return view('backend.management.delivery_methods.list', [
                'deliveryMethods' => $deliveryMethods,
                'managementPage' => true
            ]);
        }
    }
