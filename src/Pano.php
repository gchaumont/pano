<?php

namespace Pano;

use Illuminate\Support\Facades\Route;
use Pano\Controllers\PanoController;
use Pano\Controllers\ResourceController;

 class Pano
 {
     private array $applications = [];

     private array $route_prefixes = [];

     private string $currentApp;

     public function __construct()
     {
     }

     public function register(
         Application $app,
         string $path,
         string $route = null,
         string $domain = null,
         array $middleware = []
     ): static {
         $this->applications[$app->name] = $app;

         if ($route) {
             $this->route_prefixes[] = $route;
             $this->applications[$app->name]->withParentRoute($route);
         }

         Route::group([
             'prefix' => $path,
             'as' => $route,
             'domain' => $domain,
             'middleware' => array_merge($middleware, ['pano:'.$app->name]),
         ], function () use ($app) {
             Routes::application($app);

             Route::group(['as' => '.'.$app->name, 'prefix' => $app->uriKey().'/api'], function () {
                 Route::get('config', [PanoController::class, 'config'])->name('.config');
                 Route::get('search', [PanoController::class, 'search'])->name('.search');
                 // Route::get('apps', [PanoController::class, 'apps'])->name('.apps');

                 Route::group(['prefix' => 'resources'], function () {
                     Route::get('{resource}', [ResourceController::class, 'index'])->name('.apiResource');
                 });
             });
         });

         return $this;
     }

     /**
      * Generate a route to any resource in any Pano application.
      */
     public function route(string $application, string $resource = null, string|int $identifier = null): string
     {
         return $this->resolveApp($application)->route($resource, $identifier);
     }

     /**
      * Resolve an application
      * Resolve nested apps with dot-notation.
      */
     public function resolveApp(string $application): Application
     {
         foreach ($this->route_prefixes as $prefix) {
             if (str_starts_with($application, $prefix.'.')) {
                 $application = substr($application, strlen($prefix) + 1);

                 continue;
             }
         }

         $appPath = explode('.', $application);

         $resolver = $this;

         if (request()->wantsJson()) {
             response($appPath)->send();
         }
         while (!empty($appPath)) {
             $app = $resolver->application(array_shift($appPath));
             $resolver = $app;
         }

         return $app;
     }

     public function application(string $name): Application
     {
         if (empty($this->applications[$name])) {
             throw new \Exception("The application [{$name}] is not registered globally");
         }

         return $this->applications[$name];
     }

     public function currentApp(): Application
     {
         return $this->application($this->currentApp);
     }

     public function applications(): array
     {
         return $this->applications;
     }

     public function getApplications()
     {
         return $this->applications();
     }

     public function setCurrentApp(string $name): static
     {
         $appPath = explode(':', $name);

         $rootApp = array_shift($appPath);

         $this->currentApp = $rootApp;

         return $this;
     }
 }
