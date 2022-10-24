<?php

namespace Pano\Query\Handlers;

use Pano\Fields\Field;
use Pano\Fields\Relation\Relation;
use Pano\Query\QueryResult;
use Pano\Query\SearchQuery;

class ElasticoQueryHandler extends ResourceQueryHandler
{
    public function related(Relation $relation, string $key): static
    {
        $object = $this->resource->model::query()->find($key);

        $this->resource = $relation->getResource();

        $this->query = $object->queryRelated($relation->field());

        return $this;
    }

    public function entities(
        array $fields,
        int $limit,
        int $skip,
        ?string $query,
        ?Field $sorting = null,
        bool $order = true,
    ): QueryResult {
        $builder = $this->query ?? $this->resource->model::query()
            // ->select(collect($fields)->map(fn ($f) => $f->field()))

        ;

        $searchQuery = new SearchQuery($builder);

        $searchQuery->patternDirectives(...$this->resource->getDirectives(request()));

        $builder = $searchQuery->applyQueryToBuilder(request()->input('search') ?? '');

        $total = $builder->count();

        if (!empty($sorting)) {
            $builder->orderBy($sorting->field(), $order ? 'asc' : 'desc');
        }

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
            total: $total,
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
