<?php

namespace Pano\Fields;

 /**
  * Shows Textarea form.
  */
 class Textarea extends Field
 {
     protected array $suggestions;

     protected bool $alwaysShow = false;
     protected int $rows = 3;

     public function alwaysShow(array $alwaysShow): static
     {
         $this->alwaysShow = $alwaysShow;

         return $this;
     }

     public function rows(int $count): static
     {
         $this->rows = $count;

         return $this;
     }
 }
