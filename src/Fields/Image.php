<?php

namespace Pano\Fields;

 class Image extends File
 {
     public bool $canDownload = true;

     public int $maxWidth;

     public function disableDownload(): static
     {
         $this->canDownload = false;

         return $this;
     }

     public function maxWidth(int $width): static
     {
         $this->maxWidth = $width;

         return $this;
     }
 }
