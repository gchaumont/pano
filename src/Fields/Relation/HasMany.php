<?php

namespace Pano\Fields\Relation;

use Elastico\Models\DataAccessObject;

 class HasMany extends Relation
 {
     const TYPE = 'has-many';

     /**
      *  Enable display of default or custom metrics
      *  on related resource pages.
      */
     public function withMetrics(bool|array $metrics = true): static
     {
         // code...
     }

     /**
      *  Display custom fields on related
      *  resource pages.
      */
     public function withFields(array $fields): static
     {
         // code...
     }

     // public function formatValue(mixed $object): mixed
     // {
     //     if (!empty($object)) {
     //         return [
     //             'id' => $object->get_id(),
     //             'title' => $this->getResource()->getTitle($object),
     //             'link' => $this->getResource()->linkTo($object),
     //         ];
     //     }

     //     return null;
     // }

     public function getForeignKey(): string
     {
         return $this->field.'.'.$this->getResource()->model::getForeignKey();
     }

     // public function serialiseValue(DataAccessObject $object): mixed
     // {
     //     return $this->getResource()->getTitle($object->{$this->field});

     //     return $object;
     //     $value = $object->getFieldValue($this->getForeignKey());

     //     // $class = $object->getClassForRelation($this->field) ?? $this->model;
     //     // response($this->resource)->send();

     //     return $class::query()->find($value);
     // }

     public function title(DataAccessObject $object): string
     {
         return $object->{$this->name};
     }

     public function fields(): array
     {
         return [];
     }

     // public function formatValue(mixed $value): mixed
     // {
     //     response(json_encode($value))->send();
     //     dd($value);
     // }

     public function jsonConfig(): array
     {
         return [
             ...parent::jsonConfig(),
             // 'path' => $this->getResource()->url(),
         ];
     }
 }
