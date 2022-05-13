<?php

namespace Pano\Fields;

 /**
  * Shows UiAvatar field
  * https://github.com/basecamp/trix.
  */
 class UiAvatar extends Field
 {
     protected string $source = 'name';

     protected bool $squared = false;

     protected mixed $resolveUsing;

     /**
      * Overwrite from Field model.
      */
     public function key(string $key): static
     {
         parent::key($key);
         $this->source = $key;
     }

     public function resolveUsing(callable $callable): static
     {
         $this->resolveUsing = $callable;

         return $this;
     }

     public function squared(): static
     {
         $this->squared = true;

         return $this;
     }

     public function fontSize(int|float $size): static
     {
         // code...
     }

     public function bold(): static
     {
         // code...
     }

     public function backgroundColor(string $color): static
     {
         // code...
     }

     public function color(string $color): static
     {
         // code...
     }
 }
