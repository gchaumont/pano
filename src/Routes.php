<?php

namespace Pano;

use Illuminate\Support\Facades\Route;
use Pano\Controllers\ApplicationController;
use Pano\Controllers\DashboardController;
use Pano\Controllers\ResourceController;
use Pano\Menu\MenuGroup;
use Pano\Menu\MenuItems;

 class Routes
 {
     public static function application(Application $app): void
     {
         Route::group(['as' => '.'.$app->uriKey(), 'prefix' => $app->uriKey()], function () use ($app) {
             Route::get('', [ApplicationController::class, 'home'])->name('');

             static::menu($app->loadMenu()->items);
         });
     }

     public static function applicationMap(Application $app): array
     {
         $map = static::menuMap($app->loadMenu(), $app->getAppUrl());
         $map[] = [
             'path' => $app->getAppUrl(),
             'type' => 'App',
             'app' => $app->uriKey(),
         ];

         return $map;
     }

     public static function menuMap(MenuItems $menuItems, string $path = ''): array
     {
         $map = [];

         if (static::dashboardKeys($menuItems->items)) {
             $map[] = [
                 'path' => $path,
                 'type' => 'Dashboards',
                 'keys' => static::dashboardKeys($menuItems->items),
             ];
         }

         if (static::resourceKeys($menuItems->items)) {
             $map[] = [
                 'path' => $path,
                 'type' => 'Resources',
                 'keys' => static::resourceKeys($menuItems->items),
             ];
         }

         foreach (static::applicationItems($menuItems->items) as $appItem) {
             array_push($map, ...static::applicationMap($appItem->getApplication()));
         }

         foreach (static::groupItems($menuItems->items) as $groupItem) {
             array_push($map, ...static::menuMap($groupItem, $path.'/'.$groupItem->uriKey()));
         }

         return $map;
     }

     protected static function dashboardKeys(array $items): array
     {
         $items = array_filter($items, fn ($item) => !empty($item->dashboard));

         return array_map(fn ($item) => (new $item->dashboard())->uriKey(), $items);
     }

     protected static function menu(array $items): void
     {
         static::registerDashboards($items);

         static::registerResources($items);

         static::registerApplications($items);

         static::registerGroups($items);
     }

     protected static function registerDashboards(array $items): void
     {
         $keys = static::dashboardKeys($items);

         if ($keys) {
             Route::get('dashboards/{dashboard}', [DashboardController::class, 'show'])
                 ->name('.dashboard')
                 ->where('dashboards', implode('|', $keys))
         ;
         }
     }

     protected static function registerGroups(array $items): void
     {
         foreach (static::groupItems($items) as $group) {
             Route::group(
                 [
                     'prefix' => $group->uriKey(),
                     'as' => '.'.$group->uriKey(),
                 ],
                 function () use ($group) {
                     static::menu($group->items);
                 }
             );
         }
     }

     protected static function groupItems(array $items): array
     {
         return array_filter($items, fn ($item) => $item instanceof MenuGroup);
     }

     protected static function registerApplications(array $items): void
     {
         foreach (static::applicationItems($items) as $app) {
             static::application($app->getApplication());
         }
     }

     protected static function registerResources(array $items): void
     {
         $keys = static::resourceKeys($items);

         if ($keys) {
             static::resources($keys);
         }
     }

     protected static function resourceKeys(array $items): array
     {
         $items = array_filter($items, fn ($item) => !empty($item->resource));

         return array_map(fn ($item) => (new $item->resource())->uriKey(), $items);
     }

     protected static function applicationItems(array $items): array
     {
         return array_filter($items, fn ($item) => !empty($item->application));
     }

     protected static function resources(array $keys): void
     {
         Route::get('{resource}', [ResourceController::class, 'app'])
             ->name('.resources.index')
             ->where('resource', implode('|', $keys))
         ;
         Route::post('{resource}', [ResourceController::class, 'store'])
             ->name('.resources.store')
             ->where('resource', implode('|', $keys))
         ;
         Route::get('{resource}/create', [ResourceController::class, 'app'])
             ->name('.resources.create')
             ->where('resource', implode('|', $keys))
         ;
         Route::get('{resource}/{object}', [ResourceController::class, 'app'])
             ->name('.resources.show')
             ->where('resource', implode('|', $keys))
         ;
         Route::put('{resource}/{object}', [ResourceController::class, 'app'])
             ->name('.resources.update')
             ->where('resource', implode('|', $keys))
         ;
         Route::delete('{resource}/{object}', [ResourceController::class, 'destroy'])
             ->name('.resources.destroy')
             ->where('resource', implode('|', $keys))
         ;
         Route::get('{resource}/{object}/edit', [ResourceController::class, 'app'])
             ->name('.resources.edit')
             ->where('resource', implode('|', $keys))
         ;
     }
 }
