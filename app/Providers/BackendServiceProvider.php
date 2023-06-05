<?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;

    use Backend;

    class BackendServiceProvider extends ServiceProvider
    {
        public function boot()
        {
            $this->app->booted(function () {
                Backend::loadMenuItems();
            });
        }

        public function register()
        {
            
        }
    }
