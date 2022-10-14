<?php

namespace Pano\Query\Handlers;

use Pano\Fields\Relation\Relation;
use Pano\Query\QueryResult;
use Pano\Query\SearchQuery;

class ElasticoQueryHandler extends ResourceQueryHandler
{
    public function related(Relation $relation, string $key): static
    {
    }

    public function entities(array $fields, int $limit, int $skip, ?string $query): QueryResult
    {
        $builder = $this->resource->model::query()
            // ->select(collect($fields)->map(fn ($f) => $f->field()))

        ;

        $searchQuery = new SearchQuery($builder);

        $searchQuery->patternDirectives(...$this->resource->getDirectives(request()));

        $builder = $searchQuery->applyQueryToBuilder(request()->input('search') ?? '');

        // try {
        //     $hits = $searchQuery->search(request()->input('search') ?? '');
        // } catch (\Throwable $e) {
        //     return [
        //         'resource' => $resource->jsonConfig(),
        //         'fields' => collect($resource->fieldsForIndex(request()))->map(fn ($f) => $f->jsonConfig(request())),
        //         'error' => [
        //             'message' => $e->getMessage(),
        //         ],
        //     ];
        // }

        return $this->result(
            total: $builder->count(),
            hits: $builder
                ->take($limit)
                ->skip($skip)
                ->get()
                ->all(),
        );
    }

    public function idQuery(array $ids, array $fields): QueryResult
    {
        return $this->result(
            hits: $hits = $this->resource->model::query()->findMany($ids)->all(),
            total: count($hits),
        );
    }
}
