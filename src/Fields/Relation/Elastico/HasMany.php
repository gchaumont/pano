<?php

namespace Pano\Fields\Relation\Elastico;

use Elastico\Models\DataAccessObject;
use Elastico\Query\Response\Collection;
use Pano\Fields\Relation\RelatesToMany;
use Pano\Query\Handlers\ElasticoQueryHandler;
use Pano\Query\Handlers\ResourceQueryHandler;
use Pano\Resource\Resource;

class HasMany extends RelatesToMany
{
    const TYPE = 'relates-to-many';

    public string $foreignKey;

    public \Closure|bool $visibleOnIndex = false;

    public function load(array $objects): array
    {
        return Collection::make($objects)->load($this->field())->all();
    }

    public function query(Resource $resource, string $key): ResourceQueryHandler
    {
        $class = $resource->model;

        return (new ElasticoQueryHandler($resource))->related(relation: $this, key: $key);

        return (new $class())->setKey($key)->queryRelated($this->field());

        return $resource->model::query()->where();
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
     //             'title' => $this->getResource()->getTitle($object),
     //             'link' => $this->getResource()->linkTo($object),
     //         ];
     //     }

     //     return null;
    // }

    public function getForeignKey(): string
    {
        return $this->foreignKey ?? $this->field.'.'.$this->getResource()->getModel()->getForeignKey();
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
