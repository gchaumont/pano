<?php

namespace Pano\Fields;

 /**
  * Shows date picker (with time).
  */
 class DateTime extends Field
 {
     const TYPE = 'date';

     public function formatValue(mixed $value): mixed
     {
         if ($value) {
             return $value->format('Y-M-d h:m:s');
         }

         return $value;
     }
 }
