<?php

    namespace App\Http\Middleware;
	
	use Illuminate\Support\Facades\Auth;
	use Closure;
	use App;
	use Session;
	
	class Language
	{
		public function handle($request, Closure $next)
		{
			if(Auth::check()) {
				if(file_exists(resource_path('lang/' . Auth::user()->language))) {
					App::setLocale(Auth::user()->language);
				}
			} else if(Session::get('locale')) {
				$locale = Session::get('locale');

				if(file_exists(resource_path('lang/' . $locale))) {
					App::setLocale($locale);
				}
			}
			
			return $next($request);
		}
	}