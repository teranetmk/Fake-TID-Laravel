<?php

    namespace App\Http\Controllers\App;

    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;

    use App\Models\Article;
    use App\Models\Setting;

    use Mail;
    
    class DefaultController extends Controller
    {
        public function __construct() {
            if(Setting::get('app.access_only_for_users', false)) {
                $this->middleware('auth');
            }
        }

        public function showIndex($pageNumber = 0) {
            $articles = Article::orderByDesc('updated_at')->paginate(10, ['*'], 'page', $pageNumber);

            if($pageNumber > $articles->lastPage() || $pageNumber <= 0) {
                return redirect()->route('index', 1);
            }
            
            return view('frontend.index', [
                'articles' => $articles
            ]);
         }
    }
