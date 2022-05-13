<?php

namespace Pano\Menu;

use Pano\Application;

 class MenuItems
 {
     public function __construct(
         public array $items,
     ) {
     }

     public function routeMap(): array
     {
         return [
             'dashboards' => [
                 dash::class => 'route.name',
             ],
         ];
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
             $item->withConfig(app: $app, route: $this->route());
         }

         return $this;
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
