<?php

namespace Pano\Fields\Relation;

use Elastico\Models\DataAccessObject;
use Elastico\Models\Model;
use Pano\Query\Directives\Directive;
use Pano\Query\Directives\FieldDirective;

 class BelongsTo extends Relation
 {
     protected string $foreignKey;

     public function getDirective(): null|Directive
     {
         if (!empty($this->getForeignKey())) {
             return new FieldDirective($this->getForeignKey());
         }

         return null;
     }

     public function formatValue(mixed $object): mixed
     {
         if (is_string($object)) {
             return [
                 'id' => $object,
                 'title' => $object,
                 'link' => $this->getResource()->linkTo($object),
             ];
         }
         if (!empty($object)) {
             return [
                 'id' => $object->getKey(),
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
