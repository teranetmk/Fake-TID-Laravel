<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class HomePageController extends Controller
{
    public function showIndex( Request $request )
    {
	    $news = Article::select(['title', 'body'])->orderByDesc('updated_at')->limit(10)->get();
		
        return view('frontend.homepage', ['news' => $news]); 
    }
}
