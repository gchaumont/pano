<?php

namespace Pano\Fields\Relation\Eloquent;

use Elastico\Models\DataAccessObject;
use Illuminate\Database\Eloquent\Collection;
use Pano\Fields\Relation\RelatesToMany;
use Pano\Query\Handlers\EloquentQueryHandler;
use Pano\Query\Handlers\ResourceQueryHandler;
use Pano\Resource\Resource;

class HasMany extends RelatesToMany
{
    public string $foreignKey;

    public \Closure|bool $visibleOnIndex = false;

    public function load(array $objects): array
    {
        return Collection::make($objects)->load($this->field())->all();
    }

    public function query(Resource $resource, string $key): ResourceQueryHandler
    {
        // $class = $resource->model;
        // $model = (new $class());

        return (new EloquentQueryHandler($resource))->related(relation: $this, key: $key);

        return $model->setAttribute($model->getKeyName(), $key)->{$this->field()}();
    }

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
     //             'title' => $this->getRelatedResource()->getTitle($object),
     //             'link' => $this->getRelatedResource()->linkTo($object),
     //         ];
     //     }

     //     return null;
    // }

    public function getForeignKey(): string
    {
        return $this->foreignKey ?? $this->field.'.'.$this->getRelatedResource()->getModel()->getForeignKey();
    }

    public function foreignKey(string $foreignKey): static
    {
        $this->foreignKey = $foreignKey;

        return $this;
    }

    // public function serialiseValue(DataAccessObject $object): mixed
    // {
     //     return $this->getRelatedResource()->getTitle($object->{$this->field});

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

    public function config(): array
    {
        return [
            ...parent::config(),
            // 'resource' => $this->getRelatedResource(),
            // 'path' => $this->getRelatedResource()->url(),
        ];
    }
}
