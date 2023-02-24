<?php

namespace Pano\Resource;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Pano\Components\Props\QueryParameter;
use Pano\Concerns\HasBreadcrumbs;
use Pano\Concerns\Routable;
use Pano\Controllers\ResourceController;
use Pano\Endpoints\Endpoint;
use Pano\Fields\Field;
use Pano\Fields\Groups\Stack;
use Pano\Fields\Relation\RelatesToMany;
use Pano\Fields\Relation\Relation;
use Pano\Metrics\Metric;
use Pano\Pages\Page;

abstract class Resource extends Page
{
    use Routable;
    use HasBreadcrumbs;

    public string $component = 'ResourceSkeleton';

    public string $group;

    public bool $showColumnBorders = true;

    public string $tableStyle = 'tight';

    public string $icon;

    public static string $title = 'name';

    public static null|string $subtitle = null;
    /**
     * Columns that should be searched.
     */
    public array $search = ['id'];

    // Polling
    public bool $polling = true;

    // Polling seconds
    public int $pollingInterval = 5;

    // Search
    public $debounce = 0.5; // 0.5 seconds

    public $showPollingToggle = true;

    // Eager load relations
    public array $with = [];

    public bool $default_order = true;

    public null|string $default_sort_field = null;

    protected string $model;

    protected int $perPage = 50;

    protected Collection $metrics;

    protected Collection $endpoints;

    public function __construct()
    {
        // $this->id = $id ?? ;
        // $this->route($this->getRoute());
    }

    // public function getId(): string
    // {
    //     return $this->id ?? $this->setId('resources:'.Str::plural(Str::slug(class_basename($this->getModel()))))->id;
    // }

    public function newQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function getName(): string
    {
        return Str::plural(Str::headline(class_basename($this->model)));
    }

    public function getIcon(): ?string
    {
        return !empty($this->icon) ? $this->icon.'-icon' : null;
    }

    public function getModel(): Model
    {
        $class = $this->model;

        return new $class();
    }

    public function getTitle($object): string
    {
        return $object->getAttribute(static::$title) ?? $object->getKey();
    }

    public function getSubtitle($object): ?string
    {
        if (!empty(static::$subtitle)) {
            return $object->getAttribute(static::$subtitle);
        }

        return null;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function getUriKey(): string
    {
        return Str::slug($this->getName());
    }

    public function getRouteKey(): string
    {
        return Str::slug(class_basename($this->model));
    }

    public function actions(): array
    {
        return [
            ExportAsCsv::make(),
        ];
    }

    public function fields(): array
    {
        return [];
    }

    public function metrics(): array
    {
        return [];
    }

    public static function indexQuery(PanoRequest $request, Builder $query): Builder
    {
        return $query;
    }

    // Executed when another model is searching this one as relation
    public static function relatableQuery(PanoRequest $request, Builder $query): Builder
    {
        return $query;
    }

    public static function afterCreate(PanoRequest $request, Model $model)
    {
        $model->sendEmailVerificationNotification();
    }

    public static function redirectAfterCreate(PanoRequest $request, $resource)
    {
        return '/resources/'.static::uriKey().'/'.$resource->getKey();
    }

    public static function redirectAfterUpdate(PanoRequest $request, $resource)
    {
        return '/resources/'.static::uriKey().'/'.$resource->getKey();
    }

    public static function redirectAfterDelete(PanoRequest $request)
    {
        return null;
    }

    public function defaultSortField($request): null|Field
    {
        return collect($this->fieldsForIndex($request))
            ->filter(fn ($field) => $field->getKey() == $this->default_sort_field)
            ->first()
        ;
    }

    public function defaultOrder(): null|bool
    {
        return $this->default_order;
    }

    public function getRelations(): BaseCollection
    {
        return $this->getFields()
            ->filter(fn ($field) => $field instanceof Relation)
        ;
    }

    public function relationsForIndex($request): array
    {
        return collect($this->fieldsForIndex($request))
            ->flatMap(function ($field) {
                if ($field instanceof Stack || $field instanceof Nested) {
                    return $field->fields();
                }

                return [$field];
            })
            ->filter(fn ($field) => $field instanceof Relation)
            ->values()
            ->all()
        ;
    }

    public function relationsForDetail($request): array
    {
        return collect($this->getFields($request))
            ->flatMap(function ($field) {
                if ($field instanceof Stack || $field instanceof Nested) {
                    return $field->fields();
                }

                return [$field];
            })
            ->filter(fn ($field) => $field instanceof RelatesToMany)
            ->values()
            ->all()
        ;
    }

    public function fieldsForIndex($request): array
    {
        return $this->getFields()
            ->filter(fn ($field) => $field->isVisibleOnIndex($request))
            ->values()
            ->all()
        ;
    }

    public function filterableFields($request): array
    {
        return $this->getFields()
            ->map(fn ($field) => $field instanceof Stack ? $field->fields() : $field)
            ->flatten()
            ->filter(fn ($field) => $field->isFilterable($request))
            ->values()
            ->all()
        ;
    }

    public function searchableFields($request): Collection
    {
        return $this->getFields()
            ->map(fn ($field) => $field instanceof Stack ? $field->fields() : $field)
            ->flatten()
            ->filter(fn ($field) => $field->isSearchable($request))
            ->values()
        ;
    }

    public function fieldsForDetail($request): array
    {
        return $this->getFields()
            ->filter(fn ($field) => $field->isVisibleOnDetail($request))
            // ->filter(fn ($field) => !$field instanceof RelatesToMany)
            ->values()
            ->all()
        ;
    }

    public function fieldsForCreate($request): array
    {
        return $this->getFields()
            ->filter(fn ($field) => $field->isVisibleOnCreate($request))
            ->values()
            ->all()
        ;
    }

    public function fieldsForUpdate($request): array
    {
        return $this->getFields()
            ->filter(fn ($field) => $field->isVisibleOnUpdate($request))
            ->values()
            ->all()
        ;
    }

    public function getMetric(string $metric): Metric
    {
        $metric = collect($this->getMetrics())->first(fn ($m) => $m->getRouteKey() == $metric);
        if (empty($metric)) {
            throw new \RuntimeException("The metric [{$metric}] was not found in the Resource ".$this->getName());
        }

        return $metric;
    }

    public function getRelated(string $relation)
    {
        return $this->getRelations()
            ->first(fn ($f) => $f->getKey() == $relation)
        ;
    }

    public function getFields(): BaseCollection
    {
        return collect($this->fields())
            ->map(fn ($field) => $field->namespace($this->getNamespace()))
        ;
    }

    public function getMetrics(): Collection
    {
        return $this->metrics ??= collect($this->metrics()); // ->keyBy(fn ($m) => $m->getKey());
    }

    public function getDirectives($request): array
    {
        return collect($this->filterableFields(request()))
            // ->flatten()
            ->map(fn ($field) => $field->getDirective())
            ->filter()
            ->values()
            ->all()
        ;
    }

    public function url(string $page = 'index', string $key = null): string
    {
        return $this->getPages()->first()->url();

        return route($this->getLocation().'.'.$page, array_filter(['object' => $key]), false);
    }

    public function linkTo(string|object $resource): string
    {
        $resource = $resource instanceof Model ? $resource->getKey() : $resource;
        // $resource = $resource instanceof EloquentModel ? $resource->getKey() : $resource;
        // response($this->getRoute('show'))->send();
        $resource = is_object($resource) ? $resource->getKey() : $resource;

        return route($this->getLocation().'.show', ['record' => $resource], false);
    }

    public function getContexts(): Collection
    {
        return $this->getPages()
            ->concat($this->getEndpoints())
            ->concat($this->getMetrics())
            ->keyBy(fn ($o, $k) => is_numeric($k) ? $o->getKey() : $k)
        ;
    }

    public function getChildren(): Collection
    {
        return $this->getPages()
        ;
    }

    /**
     * API Endpoints provided by the page.
     */
    public function getEndpoints(): Collection
    {
        return $this->endpoints ??= collect([
            // Endpoint::get('index')->handler([ResourceController::class, 'index']),
            // Endpoint::get('records/{record}')->handler([ResourceController::class, 'show'])->parameters(':record'),
            // Endpoint::get('suggest')->handler([ResourceController::class, 'suggest']),
            // Endpoint::get('suggestRelation')->handler([ResourceController::class, 'suggestRelation']),
            // Endpoint::get('records/{record}/relation/{relation}')->handler([ResourceController::class, 'relation'])->parameters(':record', ':relation'),
            // Endpoint::get('metric/{metric}')->handler([ResourceController::class, 'metric'])->parameters(':metric'),
            // Endpoint::post('store')->handler([ResourceController::class, 'show']),
            // Endpoint::post('update')->handler([ResourceController::class, 'show']),
            // Endpoint::delete('destroy')->handler([ResourceController::class, 'show']),
        ]);
    }

    public function pages(): array
    {
        return [
            Page::make('index')
                ->path('/')
                ->component('ListResource')
                ->children($this->metrics())
                ->setData([
                    'resource' => fn ($request) => app(ResourceController::class)->withResource($this)->index(),
                ]),
            Page::make('show')
                ->path('/{record}')
                ->parameters(':record')
                ->component('ShowResource')
                ->setData([
                    'record' => fn ($request) => app(ResourceController::class)->withResource($this)->show($request->input('record')),
                ])
                ->props([
                    'record' => QueryParameter::make('record'),
                ]),
        ];
    }

    public function breadcrumbs(): array
    {
        $app = $this;
        $crumbs = collect();
        while ($app = $app->getApplication()) {
            $crumbs->prepend([
                'name' => $app->getName(),
                'url' => $app->url(),
            ]);
        }

        return $crumbs->all();
    }

        public function getAlias(): null|string
        {
            if (empty($this->key)) {
                return static::class;
            }

            return null;
        }

    public function getFilters(): Collection
    {
        return collect($this->filterableFields(request()));
    }

    public function getProps(): array
    {
        return [
            ...parent::getProps(),
            'name' => $this->getName(),
            'breadcrumbs' => $this->getBreadcrumbs(),
            'actions' => [],
            // 'key' => $this->getUriKey().asd,
            'metrics' => $this->getMetrics()->map(fn ($m) => $m->config()),
            // 'route' => $this->getContext().$this->getRoute(),
            'path' => $this->url(),
            'icon' => $this->getIcon(),
            'endpoints' => $this->getEndpoints()->keyBy(fn ($c) => $c->name)->map(fn ($e) => $e->config()),
            'filters' => $this->getFilters()->map(fn ($f) => $f->jsonConfig(request())),
        ];
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            '@type' => 'resource',
            'route' => $this->getLocation(),
            // 'routes' => $this->getRoutes()
            // 'fields' => array_map(fn ($field) => $field->jsonConfig(), $this->fields()),
        ];
    }
}
