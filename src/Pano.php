<?php

namespace Pano;

use Illuminate\Support\Facades\Route;
use Pano\Application\Application;
use Pano\Controllers\PanoController;
use Pano\Controllers\ResourceController;
use Pano\Routing\Routes;

class Pano
{
    private array $applications = [];

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
        $this->applications[implode('.', [$route, $app->getRouteKey()])] = $app;

        $app->namespace($route)
            ->boot()
        ;

        Route::group([
            'prefix' => $path,
            'as' => $route.'.',
            'domain' => $domain,
            'middleware' => array_merge($middleware, ['pano:'.implode('.', [$route, $app->getRouteKey()])]),
        ], function () use ($app) {
            Routes::application($app);

            Route::group(['as' => $app->getRouteKey(), 'prefix' => $app->getUriKey().'/api'], function () {
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
        if (str_contains($application, ':')) {
            [$application, $folder] = explode(':', $application);
        }

        $paths = explode('.', $application);

        for ($i = 1; $i <= count($paths); ++$i) {
            $key = implode('.', array_slice($paths, 0, $i));

            if (array_key_exists($key, $this->applications)) {
                $app = $this->applications[$key];
                if (count($paths) > $i) {
                    return $app->application(implode('.', array_slice($paths, $i)));
                }

                return $app;
            }
        }

        throw new \Exception("The application [{$application}] is not registered");
    }

    public function resolveFromRoute(string $route): object
    {
        $parts = explode(':', $route);

        $app = $this->resolveApp($parts[0]);

        if (!empty($parts[1])) {
            $firstDot = strpos($parts[1], '.');
            $kind = substr($parts[1], 0, $firstDot);
            $routeKey = substr($parts[1], $firstDot + 1, strpos($parts[1], '.', $firstDot + 1) - $firstDot - 1);

            return match ($kind) {
                'resources' => $app->resources->first(fn ($resource) => $resource->getRouteKey() == $routeKey),
                'dashboards' => $app->dashboards->first(fn ($dashbaord) => $dashbaord->getRouteKey() == $routeKey),
            };
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

    public function setRootApp(string $rootApp): static
    {
        $this->rootApp = $rootApp;

        return $this;
    }

    public function rootApp()
    {
        return $this->applications[$this->rootApp];
    }

    public function currentApp(): Application
    {
        return $this->resolveApp(request()->route()->getName());

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
        $this->currentApp = $name;

        return $this;
    }

    public function resolveUserTimezone(): null|string
    {
        return config('app.timezone');
    }
}
