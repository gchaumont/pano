<?php

namespace Pano\Menu;

use Pano\Concerns\Linkable;

 class MenuItems
 {
     use Linkable;

     public function __construct(
         public array $items,
     ) {
     }

     public function getName(): string
     {
         return '';
     }

     public function collapsable(bool $collapsable = true): static
     {
         $this->collapsable = $collapsable;

         return $this;
     }

     public function namespace(string $namespace): static
     {
         $this->namespace = $namespace;
         foreach ($this->items as $item) {
             $item->namespace($namespace);
         }

         return $this;
     }

     public function pathPrefix(string $pathPrefix): static
     {
         $this->pathPrefix = $pathPrefix;

         foreach ($this->items as $item) {
             $item->pathPrefix($this->getPath());
         }

         return $this;
     }

     public function jsonConfig(): array
     {
         return [
             'type' => 'group',
             'name' => $this->getName(),
             'collapsable' => $this->collapsable,
             'items' => array_values(array_map(fn ($item) => $item->jsonConfig(), $this->items)),
         ];
     }
 }
