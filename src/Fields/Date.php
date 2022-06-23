<?php

namespace Pano\Fields;

 /**
  * Shows date picker (without time).
  */
 class Date extends Field
 {
     protected string $timezone;

     public function userTimezone(string $timezone): static
     {
         $this->timezone = $timezone;

         return $this;
     }

     public function formatValue(mixed $value): mixed
     {
         if ($value) {
             return $value->format('Y-M-d');
         }

         return $value;
     }
 }
