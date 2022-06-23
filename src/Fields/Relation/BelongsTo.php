<?php

namespace Pano\Fields\Relation;

use Elastico\Models\DataAccessObject;
use Elastico\Models\Model;

 class BelongsTo extends Relation
 {
     protected string $foreignKey;

     public function formatValue(mixed $object): mixed
     {
         if (!empty($object)) {
             return [
                 'id' => $object->get_id(),
                 'title' => $this->getResource()->getTitle($object),
                 'link' => $this->getResource()->linkTo($object),
             ];
         }

         return null;
     }

     public function getForeignKey(): string
     {
         return $this->foreignKey ?? $this->field.'.'.$this->getResource()->model::getForeignKey();
     }

     public function foreignKey(string $key): static
     {
         $this->foreignKey = $key;

         return $this;
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
 }
