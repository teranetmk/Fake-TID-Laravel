<?php

    namespace App\Http\Controllers\App;

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\Output;

    class CommandController extends Controller
    {
        public function migrateZacra()
        {
            Artisan::call('migrate');
            dd(Artisan::output());
        }
        
        public function clearCache()
        {
            
			Artisan::call('config:clear');
			Artisan::call('cache:clear');
			Artisan::call('route:clear');
			Artisan::call('view:clear');
            Artisan::call('optimize:clear');
			
			// Artisan::call('config:cache');
            dd(Artisan::output());
        }
        public function scheduleTask(){
            Artisan::call('schedule:run');
            dd(Artisan::output());
            
        }
    }