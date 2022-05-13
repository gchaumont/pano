<?php

namespace Pano\Menu;

use Illuminate\Support\Str;
use Pano\Application;

 class MenuGroup extends MenuItems
 {
     protected bool $collapsable = true;

     public function __construct(
         public readonly MenuItem $name,
         public array $items,
     ) {
     }

     public function uriKey(): string
     {
         return strtolower(Str::slug($this->name->name));
     }

     public function collapsable(bool $collapsable = true): static
     {
         $this->collapsable = $collapsable;

         return $this;
     }

     public function withConfig(Application $app, string $route): static
     {
         $this->app = $app->getAppRoute();
         $this->route = $route;

         foreach ($this->items as $item) {
             $item->withConfig(app: $app, route: implode('.', array_filter([$this->route(), $this->uriKey()])));
         }

         return $this;
     }

     public static function make(string|MenuItem $name, array $items): static
     {
         if (is_string($name)) {
             $name = MenuItem::make($name);
         }

         return new static(name : $name, items: $items);
     }

     public function route(): string
     {
         return $this->route;
     }

     public function jsonConfig(Application $app): array
     {
         return [
             'type' => 'group',
             'name' => $this->name->name,
             'collapsable' => $this->collapsable,
             'items' => array_values(array_map(fn ($item) => $item->jsonConfig($app), $this->items)),
         ];
     }
 }
