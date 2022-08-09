<?php

namespace Pano\Routing;

use Illuminate\Support\Facades\Route;
use Pano\Application\Application;
use Pano\Controllers\ApplicationController;
use Pano\Controllers\DashboardController;
use Pano\Controllers\ResourceController;
use Pano\Dashboards\Dashboard;
use Pano\Menu\MenuItems;
use Pano\Resource\Resource;

class Routes
{
    public static function application(Application $app): void
    {
        Route::group([
            'as' => $app->getRouteKey(),
            'prefix' => $app->getPath(),
        ], function () use ($app) {
            Route::get('', [ApplicationController::class, 'home'])->name('');

            Route::group(['as' => ':'], function () use ($app) {
                $app->resources->each(fn ($resource) => static::resource($resource));

                $app->dashboards->each(fn ($dash) => static::dashboard($dash));
            });

            Route::group(['as' => '.'], function () use ($app) {
                $app->applications->each(fn ($app) => static::application($app));
            });
        });
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

    protected static function dashboard(Dashboard $dashboard): void
    {
        Route::group([
            'prefix' => $dashboard->getPath(),
            'as' => 'dashboards.',
        ], function () use ($dashboard) {
            Route::get('', [DashboardController::class, 'show'])->name($dashboard->getRouteKey())
            ;
            Route::get('metrics/{metric}', [DashboardController::class, 'metric'])->name($dashboard->getRouteKey().'.metric');
        });
    }

    protected static function resource(Resource $resource): void
    {
        Route::group([
            'prefix' => $resource->getPath(),
            'as' => 'resources.'.$resource->getRouteKey().'.',
        ], function () {
            Route::controller(ResourceController::class)->group(function () {
                Route::get('suggest', 'suggest')->name('suggest');
                Route::get('metrics/{metric}', 'metric')->name('metric');

                Route::get('', 'index')->name('index');
                Route::post('', 'store')->name('store');
                Route::get('create', 'app')->name('create');
                Route::get('{object}', 'show')->name('show');
                Route::put('{object}', 'app')->name('update');
                Route::delete('{object}', 'destroy')->name('destroy');
                Route::get('{object}/edit', 'app')->name('edit');
                Route::get('{object}/relations/{relation}', 'relation')->name('relation');
                Route::get('{object}/relations/{relation}/suggest', 'suggest_relation')->name('suggest_relation');
            });
        });
    }
}
