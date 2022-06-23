<?php

namespace Pano;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Pano\Commands\ListApps;
use Pano\Middleware\PanoMiddleware;

 class PanoServiceProvider extends ServiceProvider
 {
     public function boot()
     {
         // Route::bind('pano-application', fn ($value) => resolve(Pano::class)->resolveApp($value));

         Route::bind('pano-resource', fn ($value) => resolve(Pano::class)->resolveApp(request()->route()->getName()));

         $this->app['view']->addNamespace('Pano', __DIR__.'/../resources/views');

         $this->app['view']->composer('Pano::pano', function ($view) {
             $view->with('pano', resolve(Pano::class));
         });

         $router = $this->app->make(Router::class);

         $this->loadViewsFrom(__DIR__.'/../resources/views', 'pano');

         $this->publishes([
             __DIR__.'/../public' => public_path('vendor/pano'),
         ], ['pano-assets']);

         $router->aliasMiddleware('pano', PanoMiddleware::class);

         $this->commands([
             ListApps::class,
         ]);
     }

     public function register()
     {
         $this->app->singleton(Pano::class, fn () => new Pano());
     }
 }
