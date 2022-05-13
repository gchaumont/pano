<?php

namespace Pano;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Pano\Commands\ListApps;
use Pano\Middleware\PanoMiddleware;

 class PanoServiceProvider extends ServiceProvider
 {
     public function boot()
     {
         $this->app->singleton(Pano::class, fn () => new Pano());

         $this->app['view']->addNamespace('Pano', __DIR__.'/../resources/views');

         $this->app['view']->composer('Pano::pano', function ($view) {
             $view->with('pano', resolve(Pano::class));
         });

         $router = $this->app->make(Router::class);

         $router->aliasMiddleware('pano', PanoMiddleware::class);

         $this->commands([
             ListApps::class,
         ]);
     }

     public function register()
     {
     }
 }
