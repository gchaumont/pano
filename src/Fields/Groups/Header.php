<?php

namespace Pano\Fields\Groups;

use Pano\Fields\Field;

 class Header extends Field
 {
     /**
      * Don't show headers on indexes.
      */
     protected bool $showInIndex = false;

     public function __construct(
         public string $name,
         public string $html,
     ) {
     }

     // Limit number of fields
     public function limit(int $limit): static
     {
         $this->limit = $limit;

         return $this;
     }
 }
