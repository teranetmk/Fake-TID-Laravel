<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\Setting;

use Config;
use App;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (app()->environment() === 'production') {
            URL::forceScheme('https');
            URL::forceRootUrl(env('APP_URL', 'https://fake-tids.su'));
        }

        Schema::defaultStringLength( 191 );

        if (Schema::hasTable( 'settings' )) {
            foreach ( Setting::all() as $setting ) {
                Config::set( $setting->key, Setting::get( $setting->key ) );
            }
        }

        // $captcha_img = captcha_img( 'math' );
        // View::share( 'captcha_img', $captcha_img );


        Validator::extend( 'recaptcha', 'App\Rules\ReCaptchaRule@passes' );
        Validator::extend( 'hcaptcha', 'App\Rules\HCaptcha@passes' );

    }

    public function register()
    {

    }
}
