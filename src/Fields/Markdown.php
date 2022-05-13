<?php

namespace Pano\Fields;

 /**
  * Displays Markdown editor.
  * use (markdown-it NPM).
  */
 class Markdown extends Field
 {
     /**
      * Don't show JSON on indexes.
      */
     public bool $showInIndex = false;

     public bool $allways_show_content = false;

     public function allwaysShow(): static
     {
         $this->allways_show_content = true;

         return $this;
     }
 }
