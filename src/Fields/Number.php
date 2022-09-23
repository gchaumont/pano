<?php

namespace Pano\Fields;

use Pano\Query\Directives\Directive;
use Pano\Query\Directives\NumericFieldDirective;

 /**
  * Shows number form.
  */
 class Number extends Field
 {
     const TYPE = 'number';

     protected null|string $textAlign = 'right';

     protected int|float $min_amount;
     protected int|float $max_amount;
     protected int|float $amount_step;

     protected string $alignment = 'right';

     public function getDirective(): null|Directive
     {
         if (!empty($this->field)) {
             return new NumericFieldDirective($this->field);
         }

         return null;
     }

     public function formatValue(mixed $value): mixed
     {
         if (is_numeric($value) && $value > 1000) {
             return number_format($value, 0, '.', '\'');
         }

         return $value;
     }

     public function min(int|float $min): static
     {
         $this->min_amount = $min;

         return $this;
     }

     public function max(int|float $max): static
     {
         $this->max_amount = $max;

         return $this;
     }

     public function step(int|float $step): static
     {
         $this->amount_step = $step;

         return $this;
     }
 }
