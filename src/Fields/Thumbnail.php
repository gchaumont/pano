<?php

namespace Pano\Fields;

 class Thumbnail extends File
 {
     public string $format = 'default';

     public function squared(): static
     {
         $this->format = 'squared';

         return $this;
     }

     public function rounded(): static
     {
         $this->format = 'rounded';

         return $this;
     }
 }
