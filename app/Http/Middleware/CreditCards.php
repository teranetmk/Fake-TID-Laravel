<?php

    namespace App\Http\Middleware;

    use Closure;

    use App\Models\Setting;
    
    class CreditCards
    {
        public function handle($request, Closure $next)
        {
            if(Setting::get('shop.creditcards.enabled', false)) {
				return $next($request);
			}

            return redirect()->route('shop');
        }
    }
