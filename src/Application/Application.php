<?php

namespace Pano\Application;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Pano\Concerns\Linkable;
use Pano\Dashboards\Dashboard;
use Pano\Menu\MenuGroup;
use Pano\Menu\MenuItem;
use Pano\Menu\MenuItems;
use Pano\Pano;
use Pano\Resource\AutoResource;
use Pano\Resource\Resource;
use Pano\Routing\Routes;
use ReflectionClass;

abstract class Application
{
    use Linkable;

    public Collection $applications;

    public Collection $resources;

    public Collection $dashboards;

    public readonly string $id;

    protected string $routePrefix;

    final public function __construct(
        null|string $id = null,
        string $name = null,
        protected null|string $homepage = null,
    ) {
        if ($name) {
            $this->name($name);
        }
        $this->id = $id ?? static::class;
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

    public function resource(string $resource): Resource
    {
        $class = $this->resources->first(fn ($r) => $r->getKey() == $resource);

        if (empty($class)) {
            throw new \Exception("The resource [{$resource}] was not found in [{$this->getName()}]");
        }

        return $class;
    }

    public function dashboard(string $dashboard): Dashboard
    {
        $class = $this->dashboards->first(fn ($r) => $r->getKey() == $dashboard);

        if (empty($class)) {
            throw new \Exception("The dashboard [{$dashboard}] was not found in [{$this->getName()}]");
        }

        return $class;
    }

    public function application(string $app): Application
    {
        if (str_contains($app, ':')) {
            $app = substr($app, 0, strpos($app, ':'));

            return $this->application($app)->application(substr($app, strpos($app, ':') + 1));
        }

        $application = $this->applications->first(fn ($r) => $r->getRouteKey() == $app);

        if (empty($application)) {
            throw new \Exception("The application '{$app}' is not registered in the app ".($this->routePrefix ?? null));
        }

        return $application;
    }

    public function homepage(): string
    {
        return $this->homepage ?? $this->dashboards->first()?->url() ?? $this->resources->first()?->url();
    }

    public function route(string $resource = null, string $identifier = null)
    {
        if (empty($resource) && empty($identifier)) {
            return route($this->getRoute(), [], false);
        }

        return route(
            $this->getRoute().'.'.$this->getResource($resource)->folderPath(),
            array_filter([
                'identifier' => $identifier,
            ]),
            false
        );
    }

    public function getAppUrl(): string
    {
        return resolve(Pano::class)->route($this->getRoute());
    }

    /**
     * Default Menus.
     */
    public function mainMenu(): array
    {
        $menu = collect();

        if (!empty($this->dashboards())) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Dashboards')->icon('dashboard'),
                    items: $this->dashboards->map(fn ($dashboard) => MenuItem::dashboard($dashboard->getKey()))->all()
                )
                    ->collapsable()
            );
        }
        if (!empty($this->resources())) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Resources')->icon('object'),
                    items: $this->resources->map(fn ($resource) => MenuItem::resource($resource->getKey())->icon($resource->icon ?? null))->all()
                )
                    ->collapsable()
            );
        }
        if (!empty($this->applications())) {
            $menu->push(
                MenuGroup::make(
                    name: MenuItem::make('Applications'),
                    items: collect($this->applications)->map(fn ($application) => MenuItem::application($application->getKey()))->all()
                )
                    ->collapsable()
            );
        }

        return $menu->all();
    }

    public function getApplications(): array
    {
        return $this->applications->all();
    }

    final public function jsonConfig(): array
    {
        $this->boot();

        return [
            // 'routes' => Routes::jsonRoutes($this),
            'name' => $this->getName(),
            'homepage' => $this->homepage(),
            'path' => $this->getAppUrl(),
            'route' => $this->getRoute(),
            // 'key' => $this->uriKey(),
            'menu' => collect($this->menu->items)->map(fn ($menu) => $menu->jsonConfig())->values()->all(),
            'resources' => $this->resources->map(fn ($resource) => $resource->jsonConfig())->values()->all(),
            'dashboards' => $this->dashboards->map(fn ($dashboard) => $dashboard->jsonConfig())->all(),
            'apps' => $this->applications->map(fn ($app) => $app->jsonConfig())->values()->all(),
        ];
    }

    public function resourcesForFolderModels(string $folder): array
    {
        throw new \Exception('Currently not available due to unique resource class requirement');

        return array_map(
            fn ($model) => $this->generateResourceForModel($model),
            $this->allModelsFromFolder($folder)
        );
    }

    public function generateResourceForModel(string $model): Resource
    {
        return new AutoResource(model: $model);
    }

    public function allModelsFromFolder(string $folder): array
    {
        return collect(File::allFiles(app_path($folder)))
            ->map(function ($item) use ($folder) {
                $path = $item->getRelativePathName();

                $folder = explode('/', $folder);
                $folder = array_map(fn ($bit) => ucfirst($bit), $folder);
                $folder = implode('/', $folder);
                $path = trim($folder.'/'.$path, '/');

                return sprintf(
                    '%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );
            })
            ->filter(function ($class) {
                // $valid = str_starts_with($class, 'App\\');

                // if ($valid && class_exists($class)) {
                // response($class)->send();
                $reflection = new ReflectionClass($class);

                return !$reflection->isAbstract(); //&&  $reflection->isSubclassOf(Model::class)
                 // }
            })->all()
         ;
    }

    public function boot(): static
    {
        $this->resources = collect($this->resources())
            ->map(fn ($resource) => $resource->namespace($this->getRoute()))
        ;

        $this->dashboards = collect($this->dashboards())
            ->map(fn ($dashboard) => $dashboard->namespace($this->getRoute()))
        ;

        $this->applications = collect($this->applications())
            ->map(fn ($app) => $app->namespace($this->getRoute()))

        ;

        $this->menu = (new MenuItems(items: $this->mainMenu()))->namespace($this->getRoute())->pathPrefix('');

        $this->applications->each(fn ($app) => $app->boot());

        return $this;
    }

    // private static function keyByClassOrID(array $objects): array
    // {
    //     return collect($objects)
    //         ->keyBy(fn ($object, $key) => is_numeric($key) ? get_class($object) : $key)
    //         ->all()
    //      ;
    // }
}
