<?php

namespace Pano\Fields;

 /**
  * Generates a slug based on another field.
  */
 class Slug extends Field
 {
     public string $separator = '_';
     public string $source;

     public function from(string $source): static
     {
         $this->source = $source;

         return $this;
     }

     public function separator(string $separator): static
     {
         $this->separator = $separator;

         return $this;
     }
 }
