<?php

namespace Pano;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Pano\Application\Application;
use Pano\Concerns\HasContexts;
use Pano\Pages\Page;

class ContextManager
{
    use HasContexts;
    /**
     * Views to add to the head
     * of a running Pano app.
     */
    private array $headViews = [];

    private Collection $stack;

    public function __construct()
    {
        $this->stack = collect();
        $this->contexts = collect();
    }

    public function manager(): static
    {
        return $this;
    }

    /**
     * Register a Routable Component in the Context Manager
     * Will make the Component available for route registration.
     */
    public function register(Page $page): static
    {
        if ($this->contexts->has($page->getId())) {
            throw new \Exception("Page [{$page->getId()}] has already been registered");
        }

        $this->contexts->put(
            key: $page->getId(),
            value: $page
        );

        $page->register();

        // dd($page::$remember);

        app(PanoServiceProvider::class, ['app' => app()])->registerRoutes();

        return $this;
    }

    public function getContexts(): Collection
    {
        return $this->contexts;
    }

    /**
     * Generate a route to any resource in any Pano application.
     */
    // public function url(string $application, string $resource = null, string|int $identifier = null): string
    // {
    //     return $this->application($application)->url($resource, $identifier);
    // }

    // public function resolveFromRoute(string $route): object
    // {
    //     $parts = explode(':', $route);

    //     $app = $this->application($parts[0]);

    //     if (!empty($parts[1])) {
    //         $routeKey = Str::before(Str::after($parts[1], '.'), '.');

    //         return match (Str::before($parts[1], '.')) {
    //             'resources' => $app->resources->first(fn ($resource) => $resource->getRoute() == 'resources.'.$routeKey),
    //             'dashboards' => $app->dashboards->first(fn ($dashbaord) => $dashbaord->getRouteKey() == 'dashboards.'.$routeKey),
    //         };
    //     }

    //     return $app;
    // }

    public function setCurrentApp(Context $rootApp): static
    {
        $this->stack->push($rootApp);
        // $this->stack->push($rootApp->boot());

        return $this;
    }

    public function getStack(): Collection
    {
        return $this->stack;
    }

    public function bootCurrentApp(): static
    {
        $this->getCurrentApp()->boot();

        return $this;
    }

    public function getCurrentApp(): Application
    {
        return $this->getStack()->last();
    }

    public function rootApp(): Context
    {
        return $this->getStack()->first();
    }

    public function stack(): Context
    {
        return $this->getStack();
    }

    public function getLocation(): ?Context
    {
        return null;
    }

    public function headView(string $view): static
    {
        $this->headViews[] = $view;

        return $this;
    }

    public function getHeadViews(): array
    {
        return $this->headViews;
    }

    public function resolveUserTimezone(): null|string
    {
        return config('app.timezone');
    }
}
