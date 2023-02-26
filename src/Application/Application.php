<?php

namespace Pano\Application;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Dashboards\Dashboard;
use Pano\Dashboards\Home;
use Pano\Facades\Pano;
use Pano\Forms\SelectMenu;
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

    public Collection $scopes;

    public string $component = 'PanoApplication';

    protected string $routePrefix;

    protected MenuItems $menu;

    protected null|string $homepage = null;

    protected string $logo;

    final public function __construct(
        null|string $key = null,
        string $name = null,
    ) {
        if ($name) {
            $this->name($name);
        }
        if ($key) {
            $this->key($key);
        }
        // $this->id($id ?? Str::slug(class_basename(static::class)));
        // $this->route($this->getId());
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

    public function dashboards(): array
    {
        return [Home::make()->key('home')];

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
        // $dashboard = Str::start($dashboard, 'dashboards:');

        if (class_exists($dashboard)) {
            return $this->getRoot()->context($dashboard);
        }

        return $this->getDashboards()->get($dashboard)
        ?? throw new \Exception("The dashboard [{$dashboard}] was not found in [{$this->getLocation()}]");
    }

    public function getResource(string $resource): Resource
    {
        // $resource = Str::start($resource, 'resources:');
        if (class_exists($resource)) {
            return $this->getRoot()->context($resource);
        }

        return $this->getResources()->get($resource)
        ?? throw new \Exception("The resource [{$resource}] was not found in [{$this->getLocation()}]");
    }

    public function getApp(string $application): Application
    {
        if (class_exists($application)) {
            return $this->getRoot()->context($application);
        }

        return $this->getApplications()->get($application)
        ?? Pano::context($application)
        ?? throw new \Exception("The application [{$application}] was not found in [{$this->getLocation()}]");
    }

    public function homepage(): string
    {
        return $this->homepage ?? $this->getDashboards()->first()?->url() ?? $this->getResources()->first()?->url() ?? '';
    }

    public function getResources(): Collection
    {
        return $this->resources ??= collect($this->resources())->keyBy(fn ($r) => $r->getKey());
    }

    public function getDashboards(): Collection
    {
        return $this->dashboards ??= collect($this->dashboards())->keyBy(fn ($r) => $r->getKey());
    }

    public function getApplications(): Collection
    {
        return $this->applications ??= collect($this->applications())->keyBy(fn ($r) => $r->getKey());
    }

    public function getAlias(): null|string
    {
        if (empty($this->key)) {
            return static::class;
        }

        return null;
    }

    public function getMenu(): MenuItems
    {
        if (isset($this->menu)) {
            return $this->menu;
        }

        $this->menu = new MenuItems($this->menu());
        if ($this->getApplication()) {
            $this->menu->items->prepend(
                MenuItem::application($this->getApplication()->getId())
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
        if ($this->getScopes()->isNotEmpty()) {
            $this->getScopes()
                ->each(fn ($scope) => $menu->push(
                    SelectMenu::make($scope->getName())
                        ->options(fn () => $scope->getOptions())
                ))
            ;
        }

        if ($this->getDashboards()->isNotEmpty()) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Dashboards')->icon('dashboard'),
                    items: $this->getDashboards()
                        ->map(fn ($dashboard) => MenuItem::dashboard($dashboard->getKey()))
                        ->all()
                )
                    ->collapsable()
            );
        }
        if ($this->getResources()->isNotEmpty()) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Resources')->icon('object'),
                    items: $this->getResources()
                        ->map(fn ($resource) => MenuItem::resource($resource->getKey()))
                        ->all()
                )
                    ->collapsable()
            );
        }
        if ($this->getApplications()->isNotEmpty()) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Applications'),
                    items: $this->getApplications()
                        ->map(fn ($application) => MenuItem::application($application->getKey()))
                        ->all()
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

    final public function getProps(): array
    {
        return [
            'name' => $this->getName(),
            'isRoot' => empty($this->getApplication()),
            'homepage' => $this->homepage(),
            'path' => $this->url(),
            'route' => $this->getRoute(),
            // 'routes' => $this->getRoutes(),
            'menu' => $this->getMenu()->items->map(fn (MenuItem|MenuGroup|SelectMenu $menu) => $menu->config())->values()->all(),
            'resources' => $this->getResources()->map(fn ($resource) => $resource->config())->values()->all(),
            'dashboards' => $this->getDashboards()->map(fn ($dashboard) => $dashboard->config())->values()->all(),
            'apps' => $this->getApplications()->map(fn ($app) => $app->config())->values()->all(),
            'root' => $this->getRoot()->url(),
            'location' => $this->getLocation(),
            'icon' => $this->getIcon(),
            'logo' => $this->getLogo(),
            'scopes' => $this->getScopes(),
            // 'settings'
        ];
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            '@type' => 'Application',
        ];
    }

    public function scopes(): array
    {
        return [];
    }

    public function getScopes(): Collection
    {
        return $this->scopes ??= collect($this->scopes());
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
            // ->concat($this->getScopes())
            // ->concat($this->getPages())
            ->push($this->getMenu())
            ->filter()
            ->keyBy(fn ($c) => $c->getKey())
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
            )->props(['app' => $this->getProps()]))
        ;
    }

    // public function getRouteRecords(): Collection
    // {
    //     return parent::getRouteRecords()
    //         // ->when($this->url() != $this->homepage(), fn ($records) => $records->push(RouteRecord::redirect(path: $this->url(), redirect: $this->homepage())))
    //     ;
    // }
}
