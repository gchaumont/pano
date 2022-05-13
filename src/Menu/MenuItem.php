<?php

namespace Pano\Menu;

use Pano\Application;
use Pano\Pano;

 class MenuItem
 {
     protected string $icon;
     protected string $path;

     protected bool $canSee;

     public function __construct(
         public string $name,
     ) {
     }

     public function path(string $path): static
     {
         $this->path = $path;

         return $this;
     }

     public function canSee(bool|callable $canSee): bool
     {
         $this->canSee = is_bool($canSee) ? $canSee : $canSee();

         return $this;
     }

     public static function make(string $name, string $link = null)
     {
         $static = new static(name: $name);
         if ($link) {
             $static->path($link);
         }

         return $static;
     }

     public function icon(string $icon): static
     {
         $this->icon = $icon;

         return $this;
     }

     public static function link(string $name, string $path): static
     {
     }

     public static function externalLink(string $name, string $path): static
     {
         // code...
     }

     public static function application(string $app): static
     {
         $static = new static(name: $app);
         $static->application = $app;

         return $static;
     }

     public static function dashboard(string $dashboard): static
     {
         $this->dashboard = $dashboard;

         return $this;
     }

     public static function resource(string $resource): static
     {
         $static = new static((new $resource())->name());
         $static->resource = $resource;

         return $static;
     }

     public function url(Application $app): string
     {
         if (!empty($this->resource)) {
             return $this->resource->url($app);
         }

         return '';
     }

     public function withConfig(Application $app, string $route): static
     {
         $this->app = $app->getAppRoute();
         $this->route = $route;

         return $this;
     }

     public function getApplication(): Application
     {
         return resolve(Pano::class)->resolveApp($this->app.'.'.$this->application);
     }

     public function jsonConfig(Application $app): array
     {
         if (!empty($this->application)) {
             $this->name = $this->getApplication()->appName();
             $this->path = $this->getApplication()->getAppUrl();
         } elseif ($this->resource) {
             $this->name = (new $this->resource())->name();
             $this->path = route($this->route.'.resources.index', ['resource' => (new $this->resource())->uriKey()], [], false);
         }

         return [
             'type' => 'item',
             'format' => 'default',
             'icon' => $this->icon ?? null,
             'name' => $this->name,
             'link' => $this->path,
         ];
     }
 }
