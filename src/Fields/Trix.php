<?php

namespace Pano\Fields;

 /**
  * Shows Trix field
  * https://github.com/basecamp/trix.
  */
 class Trix extends Field
 {
     protected bool $alwaysShow = false;

     public function alwaysShow(): static
     {
         $this->alwaysShow = true;

         return $this;
     }
 }
