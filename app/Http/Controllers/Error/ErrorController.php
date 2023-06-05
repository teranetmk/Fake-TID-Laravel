<?php

    namespace App\Http\Controllers\Error;

    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;
    use App\Models\Setting;

    class ErrorController extends Controller
    {
        public function sessionExpired()
        {
            return view('errors.419');
        }
        
        public function BTCConnectionError()
        {
            return view('errors.btc-connection');
        }

        public function forbidden()
        {
            return view('errors.403');
        }

        public function notFound()
        {
            
            return view('errors.404');
        }
        
        public function fatal()
        {
            return view('errors.500');
        }
        
        public function serviceUnavailable()
        {
            return view('errors.503');
        }

        public function noPermissions() {
            return view('errors.no_permissions');
        }
        
        public function maintenance() 
        {
            $appMode = Setting::get('app.mode', 'live');
            if ($appMode === 'live') {
                return redirect()
                    ->route('home_page');
            }

            return view('errors.maintenance');
        }
    }
