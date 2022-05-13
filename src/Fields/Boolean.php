<?php

namespace Pano\Fields;

 class Boolean extends Field
 {
     protected bool|string $true_value = true;

     protected bool|string $false_value = true;

     public function trueValue(bool|string $value): static
     {
         $this->true_value = $value;

         return $this;
     }

     public function falseValue(bool|string $value): static
     {
         $this->false_value = $value;

         return $this;
     }
 }
