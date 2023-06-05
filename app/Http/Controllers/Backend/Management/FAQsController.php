<?php

    namespace App\Http\Controllers\Backend\Management;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use App\Models\FAQ;
	
	use App\Rules\RuleFAQCategoryExists;

    use Validator;
    
    class FAQsController extends Controller
    {
        public function __construct() {
            $this->middleware('backend');
			$this->middleware('permission:manage_faqs');
        }

        public function deleteFAQ($id) {
            FAQ::where('id', $id)->delete();

            return redirect()->route('backend-management-faqs');
        }

        public function editFAQForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                if($request->get('faq_edit_id')) {
                    $faq = FAQ::where('id', $request->input('faq_edit_id'))->get()->first();

                    if($faq != null) {
                        $validator = Validator::make($request->all(), [
                            'faq_edit_question' => 'required|max:255',
                            'faq_edit_answer' => 'required|max:5000',
                            'faq_edit_category' => new RuleFAQCategoryExists()
                        ]);
        
                        if(!$validator->fails()) {
                            $question = $request->input('faq_edit_question');
                            $answer = $request->input('faq_edit_answer');
                            $category = $request->input('faq_edit_category');
            
                            $faq->update([
                                'answer' => $answer,
                                'question' => $question,
                                'category_id' => $category
                            ]);

                            return redirect()->route('backend-management-faq-edit', $faq->id)->with([
                                'successMessage' => __('backend/main.changes_successfully')
                            ]);
                        }
        
                        $request->flash();
                        return redirect()->route('backend-management-faq-edit', $faq->id)->withErrors($validator)->withInput();
                    }
                }
            }

            return redirect()->route('backend-management-faqs');
        }

        public function showFAQEditPage($id) {
            $faq = FAQ::where('id', $id)->get()->first();

            if($faq == null) {
                return redirect()->route('backend-management-faqs');
            }

            return view('backend.management.faqs.edit', [
                'faq' => $faq,
                'managementPage' => true
            ]);
        }

        public function addFAQForm(Request $request) {
            if ($request->getMethod() == 'POST') {
                $validator = Validator::make($request->all(), [
                    'faq_add_question' => 'required|max:255',
                    'faq_add_answer' => 'required|max:5000',
                    'faq_add_category' => new RuleFAQCategoryExists(),
                ]);

                if(!$validator->fails()) {
                    $question = $request->input('faq_add_question');
                    $answer = $request->input('faq_add_answer');
                    $category = $request->input('faq_add_category');

                    FAQ::create([
                        'answer' => $answer,
                        'question' => $question,
                        'category_id' => $category
                    ]);

                    return redirect()->route('backend-management-faq-add')->with([
                        'successMessage' => __('backend/main.added_successfully')
                    ]);
                }

                $request->flash();
                return redirect()->route('backend-management-faq-add')->withErrors($validator)->withInput();
            }

            return redirect()->route('backend-management-faq-add');
        }

        public function showFAQAddPage(Request $request) {
            return view('backend.management.faqs.add', [
                'managementPage' => true
            ]);
        }

        public function showFAQsPage(Request $request, $pageNumber = 0) {
            $faqs = FAQ::orderByDesc('created_at')->paginate(10, ['*'], 'page', $pageNumber);
            
            if($pageNumber > $faqs->lastPage() || $pageNumber <= 0) {
                return redirect()->route('backend-management-faqs-with-pageNumber', 1);
            }

            return view('backend.management.faqs.list', [
                'faqs' => $faqs,
                'managementPage' => true
            ]);
        }
    }
