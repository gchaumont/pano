<?php

namespace Pano;

 /**
  * Group of fields displayed vertically.
  */
 class Stack
 {
     public bool $showInEdit = false;

     public function __construct(
         public string $name,
         public array $fields,
     ) {
     }
 }
