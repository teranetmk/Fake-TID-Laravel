<?php

    namespace App\Http\Controllers\Backend\Management;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use App\Models\UserTicketCategory;
	
	use Validator;

    class TicketsCategoriesController extends Controller
    {
        public function __construct() {
            $this->middleware('backend');
			$this->middleware('permission:manage_tickets_categories');
        }

        public function deleteTicketCategory($id) {
            UserTicketCategory::where('id', $id)->delete();

            return redirect()->route('backend-management-tickets-categories');
		}

		public function editTicketCategoryForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('ticket_category_edit_id')) {
                    $ticketCategory = UserTicketCategory::where('id', $request->input('ticket_category_edit_id'))->get()->first();

                    if($ticketCategory != null) {
                        $validator = Validator::make($request->all(), [
                            'ticket_category_edit_name' => 'required|max:255'
                        ]);
        
                        if(!$validator->fails()) {
                            $name = $request->input('ticket_category_edit_name');
            
                            $ticketCategory->update([
                                'name' => $name
                            ]);

                            return redirect()->route('backend-management-ticket-category-edit', $ticketCategory->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-ticket-category-edit', $ticketCategory->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-tickets-categories');
        }

        public function showTicketCategoryEditPage($id) {
            $ticketCategory = UserTicketCategory::where('id', $id)->get()->first();

            if($ticketCategory == null) {
                return redirect()->route('backend-management-tickets-categories');
            }

            return view('backend.management.tickets.categories.edit', [
                'ticketCategory' => $ticketCategory,
                'managementPage' => true
            ]);
        }
		
		public function addTicketCategoryForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'ticket_category_add_name' => 'required|max:255'
                ]);

                if(!$validator->fails()) {
                    $name = $request->input('ticket_category_add_name');
                   
                   	UserTicketCategory::create([
                        'name' => $name
                    ]);

                    return redirect()->route('backend-management-ticket-category-add')->with([
                        'successMessage' => __('backend/main.added_successfully')
                    ]);
                }

                $request->flash();
                return redirect()->route('backend-management-ticket-category-add')->withErrors($validator)->withInput();
            }

            return redirect()->route('backend-management-ticket-category-add');
        }

        public function showTicketCategoryAddPage(Request $request) {
            return view('backend.management.tickets.categories.add', [
                'managementPage' => true
            ]);
        }

        public function showTicketsCategoriesPage(Request $request, $pageNumber = 0) {
            $ticketsCategories = UserTicketCategory::orderByDesc('created_at')->paginate(10, ['*'], 'page', $pageNumber);
            
            if($pageNumber > $ticketsCategories->lastPage() || $pageNumber <= 0) {
                return redirect()->route('backend-management-tickets-categories-with-pageNumber', 1);
            }

            return view('backend.management.tickets.categories.list', [
                'ticketsCategories' => $ticketsCategories,
                'managementPage' => true
            ]);
        }
    }
