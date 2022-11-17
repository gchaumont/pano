<?php

namespace Pano\Fields;

use DateTime;
use DateTimeImmutable;
use Pano\Query\Directives\DateFieldDirective;
use Pano\Query\Directives\Directive;

 /**
  * Shows date picker (without time).
  */
 class Date extends Field
 {
     protected string $timezone;

     public function getDirective(): null|Directive
     {
         if (!empty($this->field)) {
             return new DateFieldDirective($this->field);
         }

         return null;
     }

     public function userTimezone(string $timezone): static
     {
         $this->timezone = $timezone;

         return $this;
     }

     public function formatValue(mixed $value): mixed
     {
         if ($value instanceof DateTime || $value instanceof DateTimeImmutable) {
             return [
                 'raw' => $value->format('Y-M-d'),
                 'date' => $value->format('Y M d'),
                 'time' => null,
             ];
         }

         return ['raw' => $value];
     }
 }
