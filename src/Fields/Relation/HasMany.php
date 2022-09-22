<?php

namespace Pano\Fields\Relation;

use Closure;
use Elastico\Models\DataAccessObject;

 class HasMany extends Relation
 {
     const TYPE = 'has-many';

     public string $foreignKey;

     public Closure|bool $visibleOnIndex = false;

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
     //             'id' => $object->getKey(),
     //             'title' => $this->getResource()->getTitle($object),
     //             'link' => $this->getResource()->linkTo($object),
     //         ];
     //     }

     //     return null;
     // }

     public function getForeignKey(): string
     {
         return $this->foreignKey ?? $this->field.'.'.$this->getResource()->model::getForeignKey();
     }

     public function foreignKey(string $foreignKey): static
     {
         $this->foreignKey = $foreignKey;

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

     public function jsonConfig($request): array
     {
         return [
             ...parent::jsonConfig($request),
             // 'resource' => $this->getResource(),
             // 'path' => $this->getResource()->url(),
         ];
     }
 }
