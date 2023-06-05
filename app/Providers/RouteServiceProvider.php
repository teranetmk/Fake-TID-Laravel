<?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Route;
    use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

    class RouteServiceProvider extends ServiceProvider
    {
        protected $namespace = 'App\Http\Controllers';

        public function boot()
        {
           parent::boot();
	}

        public function map()
        {
            $this->mapApiRoutes();

            $this->mapWebRoutes();
        }

        protected function mapWebRoutes()
        {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(function () {
                    require __DIR__ . '/../../routes/web.php';

                    $webFiles = glob(__DIR__ . '/../../routes/web/**/*.php');
                    $signleWebFiles = glob(__DIR__ . '/../../routes/web/*.php');

                    foreach (array_merge($webFiles, $signleWebFiles) as $routeFile) {
                        require $routeFile;
                    }
                });
        }

        protected function mapApiRoutes()
        {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        }
    }
