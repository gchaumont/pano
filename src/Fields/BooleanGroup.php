<?php

namespace Pano\Fields;

 /**
  * Group of boolean values, stored as json in SQL
  * How stored in Elastic ? array of bool? nested objects?
  */
 class BooleanGroup extends Field
 {
     protected array $value_options = [];

     protected bool $hide_false_values = false;
     protected bool $hide_true_values = false;

     public function options(array $options): static
     {
         $this->value_options = $options;

         return $this;
     }

     public function hideFalseValues(): static
     {
         $this->hide_false_values = true;
     }

     public function hideTrueValues(): static
     {
         $this->hide_true_values = true;
     }
 }
