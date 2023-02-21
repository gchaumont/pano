<?php

namespace Pano;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Pano\Commands\ListApps;
use Pano\Commands\ListContexts;
use Pano\Facades\Pano;
use Pano\Middleware\SetupApplication;

class PanoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Route::bind('pano-application', fn ($value) => Pano::resolveApp($value));

        // Route::bind('pano-resource', fn ($value) => Pano::resolveApp(request()->route()->getName()));
        $this->app['view']->addNamespace('Pano', __DIR__.'/../resources/views');

        $this->app['view']->composer('Pano::pano', function ($view) {
            $view->with('pano', Pano::manager());
        });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pano');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/pano'),
        ], ['pano-assets']);

        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('pano', SetupApplication::class);

        $this->commands([
            ListApps::class,
            ListContexts::class,
        ]);
    }

    public function register()
    {
        $this->app->scoped('pano', fn (): ContextManager => new ContextManager());
    }
}
