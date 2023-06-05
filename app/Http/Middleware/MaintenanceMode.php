<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;


class MaintenanceMode
{
    public function handle($request, Closure $next)
    {
		
		//dd(Auth::user());
		
		if(!Auth::check())
		{
			
			$appMode = Setting::get('app.mode', 'live');
			if ($appMode === 'maintenance' && ! in_array($request->segment(1), ['maintenance', 'assets', 'admin', 'api', 'login'])) {
				return redirect()
					->route('maintenance');
			}
			else
			{
				return $next($request);
			}
		
		}
		else if(Auth::check() && Auth::user()->ís_super_admin==0)
		{
			
		
			$appMode = Setting::get('app.mode', 'live');
			if ($appMode === 'maintenance' && ! in_array($request->segment(1), ['maintenance', 'assets', 'admin', 'api', 'login'])) {
				return redirect()
					->route('maintenance');
			}
			else
			{
				return $next($request);
			}
			
		}
		else if(Auth::check() && Auth::user()->ís_super_admin==1)
		{
			
			return $next($request);
		
		}
    }
}