<?php

namespace Pano\Query\Handlers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Pano\Fields\Relation\Relation;
use Pano\Query\QueryResult;

class EloquentQueryHandler extends ResourceQueryHandler
{
    public function related(Relation $relation, string $key): static
    {
        $object = $this->resource->model::find($key);

        $this->resource = $relation->getResource();

        $this->query = $object->{$relation->field()}();

        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->query ?? $this->resource->model::query();
    }

    public function idQuery(array $ids, array $fields): QueryResult
    {
        $fields = collect($fields)->map(fn ($field) => $field->field())->all();

        return $this->result(
            hits: $hits = $this->getQuery()->select($fields)->findMany($ids)->all(),
            total: count($hits)
        );
    }

    public function entities(array $fields, int $limit, int $skip, ?string $query): QueryResult
    {
        $model = $this->resource->model;
        $modelInstance = new $model();

        $query = $this->getQuery()->select(
            collect($fields)
                ->map(fn ($field) => $modelInstance->getTable().'.'.$field->field())
                // ->pipe(fn ($r) => response($r)->send())
                ->all()
        )
            ->take($limit)
            ->skip($skip)
        ;

        return $this->result(
            hits: $query->get()->all(),
            total: $query->count()
        );
    }
}
