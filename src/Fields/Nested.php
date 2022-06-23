<?php

namespace Pano\Fields;

use Closure;
use Elastico\Models\DataAccessObject;
use Pano\Query\Directives\Directive;
use Pano\Query\Directives\NestedFieldDirective;

 /**
  * Shows Text form.
  */
 class Nested extends Field
 {
     protected array $fields;

     protected int $max;

     protected Closure|bool $visibleOnIndex = false;

     public function getDirective(): null|Directive
     {
         return new NestedFieldDirective(
             $this->field,
             directives: collect($this->fields)->map(fn ($field) => $field->getDirective())->filter()->values()->all()
         );
     }

     public function fields(array $fields): static
     {
         $this->fields = $fields;

         return $this;
     }

     public function max(int $max): static
     {
         $this->max = $max;

         return $this;
     }

     public function getFields(): array
     {
         return $this->fields;
     }

     /**
      * Prepare value to be sent to Front.
      */
     public function serialiseValue(DataAccessObject $object): mixed
     {
         $list = [];
         foreach ($object->getFieldValue($this->field()) ?? [] as $nested) {
             $entity = [];
             foreach ($this->fields as $field) {
                 $entity[] = $field->serialiseValue($nested);
             }
             $list[] = $entity;
         }

         return $list;
     }

     public function jsonConfig(): array
     {
         $config = parent::jsonConfig();
         $config['fields'] = array_map(fn ($field) => $field->jsonConfig(), $this->fields);
         $config['max'] = $this->max ?? null;

         return $config;
     }
 }
