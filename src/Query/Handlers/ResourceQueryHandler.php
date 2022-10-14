<?php

namespace Pano\Query\Handlers;

use Pano\Fields\Relation\Relation;
use Pano\Query\QueryResult;
use Pano\Resource\Resource;

abstract class ResourceQueryHandler
{
    public function __construct(
        public Resource $resource
    ) {
        // code...
    }

    abstract public function related(Relation $relation, string $key): static;

    abstract public function entities(array $fields, int $limit, int $skip, ?string $query): QueryResult;
    // abstract public function entities(array $fields, array $filters, array $sorting): QueryResult;

    abstract public function idQuery(array $ids, array $fields): QueryResult;

    public function result(array $hits, int $total): QueryResult
    {
        return new QueryResult(
            hits: $hits,
            total: $total,
        );
    }
}
