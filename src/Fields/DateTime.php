<?php

namespace Pano\Fields;

use DateTime as BaseDateTime;
use DateTimeImmutable;
use Pano\Query\Directives\DateFieldDirective;
use Pano\Query\Directives\Directive;

 /**
  * Shows date picker (with time).
  */
 class DateTime extends Field
 {
     const TYPE = 'date';

     public function getDirective(): null|Directive
     {
         if (!empty($this->field)) {
             return new DateFieldDirective($this->field);
         }

         return null;
     }

     public function formatValue(mixed $value): mixed
     {
         if ($value instanceof BaseDateTime || $value instanceof DateTimeImmutable) {
             return [
                 'raw' => $value->format('Y-M-d H:i:s'),
                 'date' => $value->format('Y M d'),
                 'time' => $value->format('H:i:s'),
             ];
         }

         return ['raw' => $value];
     }
 }
