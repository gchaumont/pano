<?php

namespace Pano\Fields\Relation\Eloquent;

use Elastico\Models\DataAccessObject;
use Elastico\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Pano\Fields\Relation\RelatesToOne;
use Pano\Query\Directives\Directive;
use Pano\Query\Directives\FieldDirective;
use Pano\Query\Handlers\ResourceQueryHandler;
use Pano\Resource\Resource;

class BelongsTo extends RelatesToOne
{
    public function load(array $models): array
    {
        return Collection::make($models)->load($this->field())->all();
    }

    public function query(Resource $resource, string $key): ResourceQueryHandler
    {
        $class = $resource->model;

        return (new $class())->setKey($key)->{$this->field()}();
    }

    public function getDirective(): null|Directive
    {
        if (!empty($this->getForeignKey())) {
            return new FieldDirective($this->getForeignKey());
        }

        return null;
    }

     public function applySearch(Builder $builder, mixed $value): Builder
     {
         if (is_callable($this->searchable)) {
             return ($this->searchable)($builder, $value);
         }

         return $builder->whereHas($this->field(), fn ($q) => $q->where('name', 'like', '%'.$value.'%'));
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
                'subtitle' => $this->getResource()->getSubtitle($object),
            ];
        }

        return null;
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
