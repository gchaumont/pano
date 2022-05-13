<?php

namespace Pano\Fields;

 /**
  * Displays code editor.
  */
 class Code extends Field
 {
     protected bool $show_as_json = false;

     protected string $code_language;

     /**
      * Don't show code snippets in indexes.
      */
     protected bool $visibleOnIndex = false;

     public function json(): static
     {
         $this->show_as_json = true;

         return $this;
     }

     public function language(string $language): static
     {
         return $this->code_language = $language;
     }
 }
