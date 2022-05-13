<?php

namespace Pano\Fields;

 class Badge extends Field
 {
     protected array $field_map = [
         'info' => 'info',
         'success' => 'success',
         'danger' => 'danger',
         'warning' => 'warning',
     ];

     protected array $type_map = [
         'info' => 'badge-blue',
         'success' => ['badge-green', 'bold'],
     ];

     public function map(array $fields): static
     {
         $this->field_map = array_merge($fields, $this->field_map);

         return $this;
     }

     public function types(array $types): static
     {
         $this->type_map = array_merge($types, $this->type_map);
     }

     public function addTypes(array $types): static
     {
     }
 }
