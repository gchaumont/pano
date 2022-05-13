<?php

namespace Pano;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Pano\Menu\MenuGroup;
use Pano\Menu\MenuItem;
use Pano\Menu\MenuItems;
use Pano\Resource\AutoResource;
use Pano\Resource\Resource;
use ReflectionClass;

 class Application
 {
     protected string $parentRoute;

     public function __construct(
         public readonly null|string $name = null,
         protected null|string $homepage = null,
     ) {
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
         $class = collect($this->resources())->first(fn ($r) => (new $r())->uriKey() == $resource);

         if (empty($class)) {
             throw new \Exception("The resource [{$resource}] was not found in ".$this->name);
         }

         return new $class();
     }

     public function application(string $app): Application
     {
         $application = collect($this->getApplications())->first(fn ($r) => $r->uriKey() == $app);

         if (empty($application)) {
             throw new \Exception("The application '{$app}' is not registered in the app ".($this->parentRoute ?? null));
         }

         return $application;
     }

     public function homepage(): string
     {
         return $this->homepage ?? '/';
     }

     public function uriKey(): string
     {
         return strtolower(Str::slug($this->name));
     }

     public function appName(): string
     {
         return $this->name;
     }

     public function getAppRoute(): string
     {
         return implode('.', array_filter([$this->parentRoute ?? null, $this->uriKey()]));
     }

     public function route(string $resource = null, string $identifier = null)
     {
         if (empty($resource) && empty($identifier)) {
             return route($this->getAppRoute(), [], false);
         }
     }

     public function getAppUrl(): string
     {
         return resolve(Pano::class)->route($this->getAppRoute());
     }

     public function withParentRoute(string $route): static
     {
         $this->parentRoute = $route;

         return $this;
     }

     public function mainMenu(): array
     {
         $menu = collect();

         if (!empty($this->dashboards())) {
             $menu->push(
                 MenuGroup::make(
                     name: MenuItem::make('Dashboards')->icon('dashboard'),
                     items: collect($this->dashboards())->map(fn ($dashboard) => MenuItem::dashboard($dashboard))->all()
                 )
                     ->collapsable()
             );
         }
         if (!empty($this->resources())) {
             $menu->push(
                 MenuGroup::make(
                     name: MenuItem::make('Resources')->icon('object'),
                     items: collect($this->resources())->map(fn ($resource) => MenuItem::resource($resource))->all()
                 )
                     ->collapsable()
             );
         }
         if (!empty($this->getApplications())) {
             $menu->push(
                 MenuGroup::make(
                     name: MenuItem::make('Applications'),
                     items: collect($this->getApplications())->map(fn ($application) => MenuItem::application($application))->all()
                 )
                     ->collapsable()
             );
         }

         return $menu->all();
     }

     public function loadMenu(): MenuItems
     {
         return (new MenuItems(items: $this->mainMenu()))->withConfig(app: $this, route: $this->getAppRoute());
     }

     final public function jsonConfig(): array
     {
         return [
             'routes' => Routes::applicationMap($this),
             'name' => $this->name,
             'homepage' => $this->homepage(),
             'path' => $this->getAppUrl(),
             'route' => $this->name,
             // 'key' => $this->uriKey(),
             'menu' => collect($this->loadMenu()->items)->map(fn ($menu) => $menu->jsonConfig($this))->values()->all(),
             'resources' => collect($this->resources())->map(fn ($resource) => (new $resource())->jsonConfig())->values()->all(),
             'dashboards' => collect($this->dashboards())->map(fn ($dashboard) => (new $dashboard())->jsonConfig())->all(),
             'apps' => array_map(fn ($app) => $app->jsonConfig(), $this->getApplications()),
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

     public function getApplications(): array
     {
         return array_map(
             fn ($app) => $app->withParentRoute(route: $this->getAppRoute()),
             static::keyByClassOrID($this->applications())
         );
     }

     private static function keyByClassOrID(array $objects): array
     {
         return collect($objects)
             ->keyBy(fn ($object, $key) => is_numeric($key) ? get_class($object) : $key)
             ->all()
         ;
     }
 }
