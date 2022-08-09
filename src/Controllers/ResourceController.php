<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;
use Elastico\Query\FullText\MultiMatchQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Pano\Application\Application;
use Pano\Fields\Groups\Stack;
use Pano\Fields\Nested;
use Pano\Fields\Relation\Relation;
use Pano\Pano;
use Pano\Query\SearchQuery;
use Pano\Resource\Resource;

class ResourceController extends Controller
{
    public Application $app;

    public function metric(string $metric)
    {
        $metric = $this->getResource()->getMetric($metric);

        // return $metric->asJson(request());

        return Cache::remember('pano:metrics:'.$metric->getRouteKey(), $metric->cacheFor(), fn () => $metric->asJson(request()));
    }

    public function suggest()
    {
        return (new SearchQuery(
            $this->getResource()->model::query()
        ))
            ->patternDirectives(
                ...$this->getDirectives($this->getResource())
            )
            ->suggest(substr(request()->input('search') ?? '', 0, -1), request()->input('p'))
        ;
    }

    public function index()
    {
        if (!request()->wantsJson()) {
            return view('Pano::pano');
        }

        $resource = $this->getResource();

        $fields = collect($resource->fieldsForIndex(request()))
            // ->filter(fn ($field) => $field->canSee())
            ->map(fn ($field) => $field->field())
            ->flatten()
            ->filter()
            ->all()
        ;

        $query = request()->query('search');

        $sortInput = request()->input('sort');

        $sortField = collect($resource->fieldsForIndex(request()))
            ->filter(fn ($field) => $field->isSortable())
            ->first(fn ($field) => $field->getKey() == trim($sortInput, '-'))
        ;

        $pageInput = request()->input('page');
        $perPage = $resource->perPage();

        // $metrics = 0 == request()->query('page') ? collect($resource->metrics()) : collect();

        $builder = $resource->model::query()
            ->select($fields)
            ->take($perPage)
            ->when($sortField, fn ($q) => $q->orderBy($sortField->field(), str_starts_with($sortInput, '-') ? 'asc' : 'desc'))
            ->when($pageInput, fn ($q) => $q->skip(($pageInput - 1) * 50))
            // ->when($query, function ($q) use ($query, $resource) {
            //     $terms = explode(' ', $query);
            //     $fields = collect($resource->fieldsForIndex(request()))
            //         // ->filter(fn ($field) => $field->isSearchable())
            //     ;

            //     foreach ($fields as $field) {
            //         if ($field instanceof Stack) {
            //             foreach ($field->fields as $field) {
            //                 if ($field->field() && $resource->model::getFieldType($field->field())->isTextSearchable()) {
            //                     foreach ($terms as $term) {
            //                         $q->searchWildcard($field->field(), $term);
            //                     }
            //                 }
            //             }
            //         } else {
            //             if ($field->field() && $resource->model::getFieldType($field->field())->isTextSearchable()) {
            //                 foreach ($terms as $term) {
            //                     $q->searchWildcard($field->field(), $term);
            //                 }
            //             }
            //         }
            //     }
            // })
            // ->when(
            //     $query,
            //     fn ($q) => $q->getQuery()->should(
            //         (new MultiMatchQuery())->fields(collect($resource->fieldsForIndex(request()))->filter(fn ($f) => true || $f->isSearchable())->map(fn ($field) => $field->field())->all())->query($query)->type('bool_prefix')
            //     )
            // )

            // ->addAggregations($metrics->map(fn ($metric) => $metric->getAggregation()))

        ;
        $searchQuery = new SearchQuery($builder);

        $searchQuery->patternDirectives(...$this->getDirectives($this->getResource()));

        try {
            $response = $searchQuery->search(request()->input('search') ?? '');
        } catch (\Throwable $e) {
            return [
                'resource' => $resource->jsonConfig(),
                'fields' => collect($resource->fieldsForIndex(request()))->map(fn ($f) => $f->jsonConfig()),
                // 'metrics' => $response->aggregations(),
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        $hits = $this
            ->loadRelations(
                $response->hits(),
                collect($resource->fieldsForIndex(request()))
            )
            ->map(fn ($hit) => $this->serialiseModel($resource, $hit, $resource->fieldsForIndex(request())))
        ;

        return [
            'hits' => $hits->all(),
            'total' => $response->total(),
            'resource' => $resource->jsonConfig(),
            'fields' => collect($resource->fieldsForIndex(request()))->map(fn ($f) => $f->jsonConfig(request())),
            // 'metrics' => $response->aggregations(),
        ];
    }

    public function suggestRelation(string $resourceID, string $relation)
    {
        $baseResource = $this->getResource()->model::query();
        $baseResource->getRelated($relation);
        $resource = $baseResource->getContainingApp()->resource($related->resource);

        return (new SearchQuery(
            $resource
        ))
            ->patternDirectives(
                ...$this->getDirectives($this->getResource())
            )
            ->suggest(substr(request()->input('search') ?? '', 0, -1), request()->input('p'))
        ;
    }

    public function relation(string $resourceID, string $relation)
    {
        $baseResource = $this->getResource();

        $related = $baseResource->getRelated($relation);

        $resource = $baseResource->getContainingApp()->resource($related->resource);

        $fields = collect($resource->fieldsForIndex(request()))
            // ->filter(fn ($field) => $field->canSee())
            ->map(fn ($field) => $field->field())
            ->flatten()
            ->filter()

            ->all()
        ;

        $query = request()->query('search');

        $sortInput = request()->input('sort');

        $sortField = collect($resource->fieldsForIndex(request()))
            ->filter(fn ($field) => $field->isSortable())
            ->first(fn ($field) => $field->getKey() == trim($sortInput, '-'))
        ;

        $pageInput = request()->input('page');
        $perPage = $resource->perPage();

        // $metrics = 0 == request()->query('page') ? collect($resource->metrics()) : collect();
        // response($related->getForeignKey())->send();
        $builder = $resource->model::query()
            ->select($fields)
            ->where($related->getForeignKey(), $resourceID)
            ->take($perPage)
            ->when($sortField, fn ($q) => $q->orderBy($sortField->field(), str_starts_with($sortInput, '-') ? 'asc' : 'desc'))
            ->when($pageInput, fn ($q) => $q->skip(($pageInput - 1) * 50))
            // ->when($query, function ($q) use ($query, $resource) {
            //     $terms = explode(' ', $query);
            //     $fields = collect($resource->fieldsForIndex(request()))
            //         // ->filter(fn ($field) => $field->isSearchable())
            //     ;

            //     foreach ($fields as $field) {
            //         if ($field instanceof Stack) {
            //             foreach ($field->fields as $field) {
            //                 if ($field->field() && $resource->model::getFieldType($field->field())->isTextSearchable()) {
            //                     foreach ($terms as $term) {
            //                         $q->searchWildcard($field->field(), $term);
            //                     }
            //                 }
            //             }
            //         } else {
            //             if ($field->field() && $resource->model::getFieldType($field->field())->isTextSearchable()) {
            //                 foreach ($terms as $term) {
            //                     $q->searchWildcard($field->field(), $term);
            //                 }
            //             }
            //         }
            //     }
            // })
            // ->when(
            //     $query,
            //     fn ($q) => $q->getQuery()->should(
            //         (new MultiMatchQuery())->fields(collect($resource->fieldsForIndex(request()))->filter(fn ($f) => true || $f->isSearchable())->map(fn ($field) => $field->field())->all())->query($query)->type('bool_prefix')
            //     )
            // )

            // ->addAggregations($metrics->map(fn ($metric) => $metric->getAggregation()))

        ;
        $searchQuery = new SearchQuery($builder);

        $searchQuery->patternDirectives(...$this->getDirectives($resource));

        $response = $searchQuery->search(request()->input('search') ?? '');

        $hits = $this
            ->loadRelations(
                $response->hits(),
                collect($resource->fieldsForIndex(request()))
            )
            ->map(fn ($hit) => $this->serialiseModel($resource, $hit, $resource->fieldsForIndex(request())))
        ;

        return [
            'hits' => $hits->all(),
            'total' => $response->total(),
            'resource' => $related->getResource()->jsonConfig(),
            'fields' => collect($related->getResource()->fieldsForIndex(request()))->map(fn ($f) => $f->jsonConfig(request())),
            // 'metrics' => $response->aggregations(),
        ];
    }

    public function show($model)
    {
        if (!request()->wantsJson()) {
            return view('Pano::pano');
        }

        $resource = $this->getResource();

        $fields = collect($resource->fieldsForDetail(request()))
            // ->filter(fn ($field) => $field->canSee())
            // ->map(fn ($field) => $field->field())
            ->filter()
        ;
        $selectFields = $fields
            ->map(fn ($field) => $field->field())
            ->flatten()
            ->all()
        ;

        $model = $resource->model::query()->select($selectFields)->find($model);

        $model = $this->loadRelations(
            [$model],
            collect($resource->fieldsForDetail(request()))
        )->first();

        return [
            'model' => $this->serialiseModel($resource, $model, $fields),
            'fields' => collect($this->getResource()->fieldsForDetail(request()))->map(fn ($f) => $f->jsonConfig(request())),
        ];
    }

    public function store(Request $request)
    {
    }

    public function update(string $item)
    {
    }

    public function destroy(string $item)
    {
    }

    protected function getResource(): Resource
    {
        return resolve(Pano::class)->resolveFromRoute(request()->route()->getName());
    }

    protected function getDirectives(Resource $resource): array
    {
        return collect($resource->filterableFields(request()))
            // ->flatten()
            ->map(fn ($field) => $field->getDirective())
            ->filter()
            ->values()
            ->all()
        ;
    }

    protected function loadRelations(iterable $hits, iterable $fields): Collection
    {
        $hits = collect($hits);

        $relations = collect($fields)->filter(fn ($field) => $field instanceof Relation || $field instanceof Nested);

        $relations = $relations->concat($fields->filter(fn ($field) => $field instanceof Stack)->map(fn ($field) => $field->fields)->flatten());

        $relations = $relations
            ->each(function ($relation) use (&$hits) {
                if ($relation instanceof Relation) {
                    $ids = $hits->map(fn ($hit) => $hit->getFieldValue($relation->getForeignKey()))->filter();
                    if ($ids->isNotEmpty()) {
                        $related = $relation->getModel()::query()
                            ->take($ids->count())
                            ->where('_id', $ids)
                            ->get()
                            ->hits()
                            ->keyBy(fn ($hit) => $hit->get_id())
                    ;

                        $hits = $hits->map(function ($hit) use ($relation, $related) {
                            if ($val = $hit->getFieldValue($relation->getForeignKey())) {
                                if ($related->get($val)) {
                                    $hit->setFieldValue($relation->field(), $related->get($val));
                                }
                            }

                            return $hit;
                        });
                    }
                } elseif ($relation instanceof Nested) {
                    foreach ($hits as $hit) {
                        if ($rel = $hit->getFieldValue($relation->field())) {
                            $hit->setFieldValue(
                                $relation->field(),
                                $this->loadRelations($rel, collect($relation->getFields()))->all()
                            );
                        }
                    }
                }
            })
        ;

        return $hits;
    }

    protected function serialiseModel(Resource $resource, mixed $hit, iterable $fields)
    {
        return [
            'title' => $resource->getTitle($hit),
            'link' => $resource->linkTo($hit->get_id()),
            'fields' => collect($fields)
                ->keyBy(fn ($field) => $field->getKey())
                ->map(fn ($field) => $field->serialiseValue($hit)),
        ]
        ;
    }
}
