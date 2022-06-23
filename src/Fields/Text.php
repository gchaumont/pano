<?php

namespace Pano\Fields;

use Pano\Query\Directives\Directive;
use Pano\Query\Directives\FieldDirective;

 /**
  * Shows Text form.
  */
 class Text extends Field
 {
     protected array $suggestions;

     protected bool $asHtml = false;
     protected bool $copyable = false;

     public function getDirective(): null|Directive
     {
         if (!empty($this->field)) {
             return new FieldDirective($this->field);
         }

         return null;
     }

     public function suggestions(array $suggestions): static
     {
         $this->suggestions = $suggestions;

         return $this;
     }

     public function asHtml(): static
     {
         $this->asHtml = true;

         return $this;
     }

     /**
      * Allow copying to the clipboard.
      */
     public function copyable(): static
     {
         $this->copyable = true;

         return $this;
     }
 }
