<?php

namespace Pano\Fields\Relation\Eloquent;

use Elastico\Models\DataAccessObject;
use Elastico\Models\Model;
use Pano\Fields\Relation\RelatesToMany;
use Pano\Query\Directives\Directive;
use Pano\Query\Directives\FieldDirective;
use Pano\Query\Handlers\EloquentQueryHandler;
use Pano\Query\Handlers\ResourceQueryHandler;
use Pano\Resource\Resource;

 class BelongsToMany extends RelatesToMany
 {
     public function load(iterable $models): iterable
     {
         return $models->load($this->field());
     }

     public function query(Resource $resource, string $key): ResourceQueryHandler
     {
         return (new EloquentQueryHandler($resource))->related(relation: $this, key: $key);
         $class = $resource->model;

         $model = new $class();

         return $model->setAttribute($model->getKeyName(), $key)->{$this->field()}();
     }

     public function getDirective(): null|Directive
     {
         if (!empty($this->getForeignKey())) {
             return new FieldDirective($this->getForeignKey());
         }

         return null;
     }

     public function formatValue(mixed $object): mixed
     {
         return $object;
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
                 'subtitle' => $this->getResource()->getSubtitle($object),
             ];
         }

         return null;
     }

     public function getForeignKey(): string
     {
         $class = $this->getResource()->model;

         return $this->foreignKey ?? $this->field.'.'.(new $class())->getForeignKey();
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
     //     $value = $object->getAttribute($this->getForeignKey());

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
 }
