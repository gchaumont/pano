<?php

namespace Pano\Application;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Dashboards\Dashboard;
use Pano\Facades\Pano;
use Pano\Menu\MenuGroup;
use Pano\Menu\MenuItem;
use Pano\Menu\MenuItems;
use Pano\Pages\Page;
use Pano\Resource\Resource;
use Pano\Routes\RouteRecord;

abstract class Application extends Page
{
    const CONTEXT_SEPARATOR = '.';

    public Collection $resources;

    public Collection $dashboards;

    public Collection $applications;

    public string $component = 'PanoApplication';

    protected string $routePrefix;

    protected MenuItems $menu;

    protected null|string $homepage = null;

    protected string $logo;

    final public function __construct(
        null|string $id = null,
        string $name = null,
    ) {
        if ($name) {
            $this->name($name);
        }
        $this->id($id ?? Str::slug(class_basename(static::class)));
        $this->route($this->getId());
    }

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function getIcon(): ?string
    {
        return !empty($this->icon) ? $this->icon.'-icon' : null;
    }

    public function getLogo(): ?string
    {
        return !empty($this->logo) ? $this->logo : null;
    }

    public function getProps(): array
    {
        return [
            'app' => $this->config(),
            // 'menu' => $this->getMenu(),
        ];
    }

    public function dashboards(): array
    {
        return [];
    }

    public function resources(): array
    {
        return [];
    }

    public function applications(): array
    {
        return [];
    }

    public function getDashboard(string $dashboard): Dashboard
    {
        $dashboard = Str::start($dashboard, 'dashboards:');

        return $this->getDashboards()->get($dashboard)
        ?? throw new \Exception("The dashboard [{$dashboard}] was not found in [{$this->getLocation()}]");
    }

    public function getResource(string $resource): Resource
    {
        $resource = Str::start($resource, 'resources:');

        return $this->getResources()->get($resource)
        ?? throw new \Exception("The resource [{$resource}] was not found in [{$this->getLocation()}]");
    }

    public function getApp(string $application): Application
    {
        return $this->getApplications()->get($application)
        ?? throw new \Exception("The application [{$application}] was not found in [{$this->getLocation()}]");
    }

    public function homepage(): string
    {
        return $this->homepage ?? $this->getDashboards()->first()?->url() ?? $this->getResources()->first()?->url() ?? '';
    }

    public function getResources(): Collection
    {
        return $this->resources ??= collect($this->resources())->keyBy(fn ($r) => $r->getId());
    }

    public function getDashboards(): Collection
    {
        return $this->dashboards ??= collect($this->dashboards())->keyBy(fn ($r) => $r->getId());
    }

    public function getApplications(): Collection
    {
        return $this->applications ??= collect($this->applications())->keyBy(fn ($r) => $r->getId());
    }

    public function getMenu(): MenuItems
    {
        if (isset($this->menu)) {
            return $this->menu;
        }
        $this->menu = new MenuItems($this->menu());
        if ($this->getApplication()) {
            $this->menu->items->prepend(
                MenuItem::make($this->getApplication()->getName())
                    ->path(fn ($app) => $this->getApplication()->url())
                    ->icon('chevron-left')
                    ->inactive(true)
            );
        }

        return $this->menu;
    }

    // public function link(string $resource = null, string $identifier = null)
    // {
    //     if (empty($resource) && empty($identifier)) {
    //         return route($this->getRoute(), [], false);
    //     }

    //     return route(
    //         $this->getRoute().'.'.$this->getResource($resource)->folderPath(),
    //         array_filter([
    //             'identifier' => $identifier,
    //         ]),
    //         false
    //     );
    // }

    /**
     * Default Menus.
     */
    public function menu(): array
    {
        $menu = collect();

        if ($this->getDashboards()->isNotEmpty()) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Dashboards')->icon('dashboard'),
                    items: $this->getDashboards()->map(fn ($dashboard) => MenuItem::dashboard($dashboard->getId()))->all()
                )
                    ->collapsable()
            );
        }
        if ($this->getResources()->isNotEmpty()) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Resources')->icon('object'),
                    items: $this->getResources()
                        ->map(fn ($resource) => MenuItem::resource($resource->getId()))->all()
                )
                    ->collapsable()
            );
        }
        if ($this->getApplications()->isNotEmpty()) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Applications'),
                    items: $this->getApplications()->map(fn ($application) => MenuItem::application($application->getId()))->all()
                )
                    ->collapsable()
            );
        }

        return $menu->all();
    }

    // public function url(): string
    // {
    //     return $this->homepage();
    // }

    public function getPages(): Collection
    {
        return collect($this->pages())
            // ->concat($this->getApplications())
            // ->concat($this->getResources())
            // ->concat($this->getDashboards())
        ;
    }

    /**
     * Provide a set of Settings that will be made available
     * in the global settings panel and in the application menu footer.
     */
    public function settings(): ApplicationSettings
    {
    }

    final public function config(): array
    {
        return [
            'name' => $this->getName(),
            'isRoot' => empty($this->getApplication()),
            'homepage' => $this->homepage(),
            'path' => $this->url(),
            'route' => $this->getRoute(),
            // 'routes' => $this->getRoutes(),
            'menu' => $this->getMenu()->items->map(fn (MenuItem|MenuGroup $menu) => $menu->config())->values()->all(),
            'resources' => $this->getResources()->map(fn ($resource) => $resource->config())->values()->all(),
            'dashboards' => $this->getDashboards()->map(fn ($dashboard) => $dashboard->config())->values()->all(),
            'apps' => $this->getApplications()->map(fn ($app) => $app->definition())->values()->all(),
            'root' => Pano::rootApp()->url(),
            'location' => $this->getLocation(),
            'icon' => $this->getIcon(),
            'logo' => $this->getLogo(),
            // 'settings'
        ];
    }

    public function definition()
    {
        return [
            '@type' => 'Application',
            ...$this->config(),
        ];
    }

    public function getContexts(): Collection
    {
        // return $this->getChildren()
        //     ->collect()
        //     ->push($this->getMenu())
        //     ->keyBy(fn ($child) => $child->getId())
        // ;

        return collect()
            ->concat($this->getResources())
            ->concat($this->getDashboards())
            ->concat($this->getApplications())
            ->concat($this->getPages())
            ->push($this->getMenu())
            ->filter()
            ->keyBy(fn ($c) => $c->getId())
        ;
    }

    public function getChildren(): Collection
    {
        return $this->children ??= collect()
            ->concat($this->getApplications())
            ->push(NavigationSplitView::make(
                homepage: $this->homepage(),
                sidebar: $this->getMenu(),
                children: collect($this->getResources())
                    ->concat($this->getDashboards())
                // ->concat($this->getPages())
            )->props(['app' => $this->config()]))
        ;
    }

    // public function getRouteRecords(): Collection
    // {
    //     return parent::getRouteRecords()
    //         // ->when($this->url() != $this->homepage(), fn ($records) => $records->push(RouteRecord::redirect(path: $this->url(), redirect: $this->homepage())))
    //     ;
    // }
}
