<?php

namespace Pano\Fields;

use Pano\Query\Directives\Directive;
use Pano\Query\Directives\FieldDirective;

 class ID extends Field
 {
     const TYPE = 'text';

     public function getDirective(): null|Directive
     {
         if (!empty($this->field)) {
             return new FieldDirective($this->field);
         }

         return null;
     }
 }
