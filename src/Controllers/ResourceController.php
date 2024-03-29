<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;
use Elastico\Query\FullText\MultiMatchQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Pano\Application\Application;
use Pano\Facades\Pano;
use Pano\Fields\Groups\Stack;
use Pano\Fields\Relation\RelatesToMany;
use Pano\Fields\Relation\Relation;
use Pano\Query\SearchQuery;
use Pano\Resource\Resource;

class ResourceController extends Controller
{
    public Application $app;

    public function metric()
    {
        $metric = Pano::context(request()->route()->getName());

        // if (empty(request()->input('search'))) {
        //     return Cache::remember(
        //         'pano:metrics:'.$metric->getRouteKey(),
        //         $metric->cacheFor(),
        //         fn () => $metric->asJson(request(), $builder)
        //     );
        // }
        $resource = $metric->getResource();

        $query = $this->applyToBuilder($resource, $resource->newQuery(), request());

        return $metric->asJson(request(), $query);
    }

    public function suggest()
    {
        return (new SearchQuery(
            $this->getResource()->model::query()
        ))
            ->patternDirectives(
                ...$this->getResource()->getDirectives(request())
            )
            ->suggest(substr(request()->input('search') ?? '', 1, -1), request()->input('p'))
        ;
    }

    public function index()
    {
        $resource = $this->getResource();

        $fields = collect($resource->fieldsForIndex(request()))
            // ->filter(fn ($field) => $field->canSee())
            // ->map(fn ($field) => $field->field())
            // ->filter(fn ($field) => !($field instanceof Relation))
            // ->flatten()
            // ->filter()
        ;

        $query = request()->query('search');

        $sortInput = request()->input('sort');

        $sortField = collect($resource->fieldsForIndex(request()))
            ->filter(fn ($field) => $field->isSortable())
            ->first(fn ($field) => $field->getKey() == trim($sortInput, '-'))
            ?? $resource->defaultSortField(request());

        $query = $resource->newFilteredQuery(request());

        // $query = $this->applyToBuilder($resource, $resource->newQuery(), request());

        // ->select($fields->map(fn ($f) => $f->field())->all())

        //         query: request()->input('search') ?? '',
        //         filters: $filters,

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
        // ;

        // $searchQuery = new SearchQuery($builder);

        // $searchQuery->patternDirectives(...$this->resource->getDirectives(request()));

        // $builder = $searchQuery->applyQueryToBuilder(request()->input('search') ?? '');

        $total = $query->count();
        $hits = $query
            ->take($resource->perPage())
            ->when(request()->has('page'), fn ($q) => $q->skip((request()->input('page') - 1) * 50))
            ->with(
                collect($resource->relationsForIndex(request()))->map(fn ($r) => $r->getKey())->all()
            )
            ->when($sortField, fn ($q) => $q->orderBy($sortField->field(), str_starts_with($sortInput, '-') ? 'asc' : 'desc'))
            ->get()
        ;

        return [
            'hits' => $hits
                ->map(fn ($hit) => $this->serialiseResource($resource, $hit, $resource->fieldsForIndex(request())))
                ->all(),
            'total' => $total,
            'resource' => $resource->config(),
            'fields' => collect($resource->fieldsForIndex(request()))->map(fn ($f) => $f->config()),
            'filterOptions' => $resource->getFilters()
                ->map(fn ($f) => [
                    ...$f->config(),
                    ...$f->getData(request())->map(fn ($data, $key) => is_callable($data) ? $data(request()) : $data),
                ]),
        ];
    }

    public function suggestRelation(string $resourceID, string $relation)
    {
        $baseResource = $this->getResource()->model::query();
        $baseResource->getRelated($relation);
        $resource = $baseResource->getContext()->resource($related->resource);

        return (new SearchQuery(
            $resource
        ))
            ->patternDirectives(
                ...$this->getResource()->getDirectives(request())
            )
            ->suggest(substr(request()->input('search') ?? '', 1, -1), request()->input('p'))
        ;
    }

    public function relation(string $record, string $relation)
    {
        $baseResource = $this->getResource();

        $relatedRecord = $baseResource->newQuery()->find($record);

        $relation = $baseResource->getRelated($relation);

        // $relatedResource = $baseResource->getContainingApp()->resource($relation->resource);
        $relatedResource = $relation->getResource();

        $fields = collect($relatedResource->fieldsForIndex(request()))
            // ->filter(fn ($field) => $field->canSee())
            // ->map(fn ($field) => $field->field())
            ->filter(fn ($field) => !($field instanceof Relation))
            ->flatten()
            ->filter()
            // ->pipe(fn ($r) => response($r)->send())

            ->all()
        ;

        $query = request()->query('search');
        $sortInput = request()->input('sort');

        $sortField = collect($relatedResource->fieldsForIndex(request()))
            ->filter(fn ($field) => $field->isSortable())
            ->first(fn ($field) => $field->getKey() == trim($sortInput, '-'))
        ;

        $sortField ??= $relatedResource->defaultSortField(request());

        $pageInput = request()->input('page');
        $perPage = $relatedResource->perPage();

        $builder = $relatedRecord->{$relation->field()}();
        $total = $builder->count();

        $hits = $builder
            ->take($relatedResource->perPage())
            ->when(request()->has('page'), fn ($q) => $q->skip((request()->input('page') - 1) * 50))
            ->get()
            ->map(fn ($hit) => $this->serialiseResource($relatedResource, $hit, $relatedResource->fieldsForIndex(request())))
        ;

        return [
            'hits' => $hits,
            'total' => $total,
            'resource' => $relatedResource->config(),
            'fields' => collect($relatedResource->fieldsForIndex(request()))->map(fn ($f) => $f->config()),
            // 'metrics' => $response->aggregations(),
        ];
    }

    public function show($model)
    {
        $resource = $this->getResource();

        $fields = collect($resource->fieldsForDetail(request()))
            // ->filter(fn ($r) => !($r instanceof Relation))
            // ->filter(fn ($field) => $field->canSee())
            // ->map(fn ($field) => $field->field())
        ;

        $propFields = $fields
            ->filter(fn ($field) => !($field instanceof RelatesToMany))
        ;

        $relations = collect($resource->relationsForDetail(request()))
            ->filter(fn ($r) => !($r instanceof RelatesToMany))
            ->values()
            // ->with($relations->map(fn ($rel) => $rel->field())->all())
        ;

        $model = $resource
            ->newQuery()
            ->find($model)
            ->load($relations->map(fn ($r) => $r->field())->all())
        ;

        if (empty($model)) {
            abort(404);
        }

        return [
            'model' => $this->serialiseResource($resource, $model, $propFields),
            'fields' => $fields
                ->map(fn ($f) => $f->config()),
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

    public function withResource(Resource $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    protected function getResource(): Resource
    {
        return $this->resource ??= Pano::context(request()->route()->getName())->getContext();

        return Pano::resolveFromRoute(request()->route()->getName());
    }

    protected function serialiseResource(Resource $resource, mixed $hit, iterable $fields)
    {
        return [
            'title' => $resource->getTitle($hit),
            'subtitle' => $resource->getSubtitle($hit),
            'link' => $resource->linkTo($hit->getKey()),
            'id' => $hit->getKey(),
            'fields' => collect($fields)
                ->keyBy(fn ($field) => $field->getKey())
                ->map(fn ($field) => $field->serialiseValue($hit)),
        ]
        ;
    }

    protected function applyToBuilder(Resource $resource, Builder $builder, $request)
    {
        $searchableFields = $resource->searchableFields($request);

        $filters = $resource->getFilters()
            ->filter(fn ($filter) => $request->has($filter->getKey()))
            ->each(fn ($filter) => $filter->applyFilter($request, $builder, $request->input($filter->getKey())))
        ;

        return $builder->when(
            !empty($request->input('search')),
            function ($query) use ($searchableFields, $request) {
                return $query->where(function ($q) use ($searchableFields, $request) {
                    $searchableFields
                        ->each(fn ($field) => $q->orWhere($field->field(), 'like', '%'.trim($request->input('search')).'%'))
                    ;
                });
            }
        );
    }
}
