<?php

namespace Pano;

 class Panel
 {
     protected int $limit;

     public function __construct(
         public string $name,
         public array $fields,
     ) {
     }

     // Limit number of fields
     public function limit(int $limit): static
     {
         $this->limit = $limit;

         return $this;
     }
 }
