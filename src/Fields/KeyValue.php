<?php

namespace Pano\Fields;

 /**
  * Flat key/value JSON fields.
  * (for simple objects).
  */
 class KeyValue extends Field
 {
     public bool $canDownload = true;
     /**
      * Don't show JSON on indexes.
      */
     public bool $showInIndex = false;

     public bool $canEditKeys = true;
     public bool $canAddRows = true;
     public bool $canDeleteRows = true;

     public function disableDownload(): static
     {
         $this->canDownload = false;

         return $this;
     }

     public function disableEditingKeys(): static
     {
         return $this->canEditKeys = false;
     }

     public function disableAddingRows(): static
     {
         return $this->canAddRows = false;
     }

     public function disableDeletingRows(): static
     {
         return $this->canDeleteRows = false;
     }
 }
