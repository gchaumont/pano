<?php

namespace Pano\Fields;

use Pano\Query\Directives\Directive;
use Pano\Query\Directives\FieldDirective;

 class Boolean extends Field
 {
     const TYPE = 'text';

     protected bool|string $true_value = true;

     protected bool|string $false_value = false;

     public function getDirective(): null|Directive
     {
         if (!empty($this->field)) {
             return new FieldDirective($this->field);
         }

         return null;
     }

     public function formatValue(mixed $val): mixed
     {
         return match ($val) {
             $this->true_value => 'true',
            $this->false_value => 'false',
            null => null
         };
     }

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
