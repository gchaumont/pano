<?php

namespace Pano\Fields;

 /**
  * Shows Text form.
  */
 class URL extends Field
 {
     protected $displayUsing;

     public function displayUsing(callable $callable): static
     {
         $this->displayUsing = $displayUsing;

         return $this;
     }
 }
