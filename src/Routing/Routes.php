<?php

namespace Pano\Routing;

use Illuminate\Support\Facades\Route;
use Pano\Controllers\DashboardController;
use Pano\Controllers\ResourceController;
use Pano\Dashboards\Dashboard;
use Pano\Menu\MenuItems;
use Pano\Resource\Resource;

class Routes
{
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
        return;
        Route::group([
            'prefix' => $dashboard->getPath(),
            'as' => $dashboard->getRoute(),
        ], function () use ($dashboard) {
            Route::get('', [DashboardController::class, 'show'])->name('')
            ;
            Route::get('metrics/{metric}', [DashboardController::class, 'metric'])->name($dashboard->getRoute().'.metric');
        });
    }

    protected static function resource(Resource $resource): void
    {
        return;
        Route::group([
            'prefix' => $resource->getPath(),
            'as' => $resource->getRoute().'.',
        ], function () {
            Route::controller(ResourceController::class)->group(function () {
                Route::get('suggest', 'suggest')->name('suggest');
                Route::get('metrics/{metric}', 'metric')->name('metric');

                Route::get('', 'index')->name('index');
                // Route::post('', 'store')->name('store');
                // Route::get('create', 'app')->name('create');
                Route::get('{object}', 'show')->name('show');
                // Route::put('{object}', 'app')->name('update');
                // Route::delete('{object}', 'destroy')->name('destroy');
                // Route::get('{object}/edit', 'app')->name('edit');
                Route::get('{object}/relations/{relation}', 'relation')->name('relation');
                Route::get('{object}/relations/{relation}/suggest', 'suggest_relation')->name('suggest_relation');
            });
        });
    }
}
