<?php

namespace Pano\Fields\Groups;

use Pano\Fields\Field;

 /**
  * Defines how lines are presented within a Stack.
  */
 class Line extends Field
 {
     public string $showAs = 'base';

     public function __construct(
         public string $name,
     ) {
     }

     public function asBase(): static
     {
         $this->showAs = 'base';

         return $this;
     }

     public function asTitle(): static
     {
         $this->showAs = 'title';

         return $this;
     }

     public function asSubtitle(): static
     {
         $this->showAs = 'subtitle';

         return $this;
     }

     public function asSmall(): static
     {
         $this->showAs = 'small';

         return $this;
     }
 }
