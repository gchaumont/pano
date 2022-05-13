<?php

namespace Pano\Fields;

 /**
  * Progress field.
  * Used internally to display waiting, running and finished queued actions.
  */
 class Status extends Field
 {
     public array $loading = ['loading'];
     public array $failed = ['failed'];
     public array $finished = ['finished'];

     public function loadingWhen(array $values): static
     {
         $this->loading = $values;

         return $this;
     }

     public function failedWhen(array $values): static
     {
         $this->failed = $values;

         return $this;
     }

     public function finishedWhen(array $values): static
     {
         $this->finished = $values;

         return $this;
     }
 }
