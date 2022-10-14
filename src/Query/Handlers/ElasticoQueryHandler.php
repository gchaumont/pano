<?php

namespace Pano\Query\Handlers;

use Pano\Fields\Relation\Relation;
use Pano\Query\QueryResult;

class ElasticoQueryHandler extends ResourceQueryHandler
{
    public function related(Relation $relation, string $key): static
    {
    }

    public function entities(array $fields, int $limit, int $skip): QueryResult
    {
        $response = $this->resource->model::query()
            // ->select(collect($fields)->map(fn ($f) => $f->field()))
            ->take($limit)
            ->skip($skip)
            ->get()
        ;

        return $this->result(
            hits: $response->all(),
            total: $response->total()
        );
    }

    public function idQuery(array $ids, array $fields): QueryResult
    {
    }
}
