<?php

namespace Pano\Resource;

use Elastico\Models\Model;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Pano\Concerns\Linkable;
use Pano\Fields\Field;
use Pano\Fields\Groups\Stack;
use Pano\Fields\Relation\Relation;
use Pano\Metrics\Metric;
use Pano\Query\Handlers\EloquentQueryHandler;
use Pano\Query\Handlers\ResourceQueryHandler;

abstract class Resource
{
    use Linkable;

    // public string $queryHandler = EloquentQueryHandler::class;
    public string $queryHandler = ResourceQueryHandler::class;

    public string $group;
    public bool $showColumnBorders = true;
    public string $tableStyle = 'tight';

    public null|string $icon = null;

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

    public function query(): ResourceQueryHandler
    {
        $class = $this->queryHandler;

        return new $class(resource: $this);
    }

    public function getName(): string
    {
        return Str::plural(Str::headline(class_basename($this->model)));
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
        return Str::slug(strtolower(class_basename($this->model)));
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
        return collect($this->fieldsForDetail($request))
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
            ->map(fn ($field) => $field instanceof Stack ? $field->fields : $field)
            ->flatten()
            ->filter(fn ($field) => $field->isFilterable($request))
            ->values()
            ->all()
        ;
    }

    public function fieldsForDetail($request): array
    {
        return $this->getFields()
            ->filter(fn ($field) => $field->isVisibleOnDetail($request))
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

    public function linkTo(string|object $resource): string
    {
        $resource = $resource instanceof Model ? $resource->getKey() : $resource;
        // $resource = $resource instanceof EloquentModel ? $resource->getKey() : $resource;
        // response($this->getRoute('show'))->send();
        $resource = is_object($resource) ? $resource->getKey() : $resource;

        return route($this->getRoute('show'), ['object' => $resource], false);
    }

    public function getRoute($endpoint = 'index'): string
    {
        return $this->namespace.':resources.'.$this->getRouteKey().'.'.$endpoint;
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

    public function getMetrics(): array
    {
        return $this->metrics ??= collect($this->metrics())->map(fn ($m) => $m->namespace($this->getRoute()))->all();
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

    public function jsonConfig(): array
    {
        return [
            'name' => $this->getName(),
            // 'key' => $this->getUriKey(),
            'metrics' => array_map(fn ($metric) => $metric->jsonConfig(), $this->getMetrics()),
            'route' => $this->getRoute(),
            'path' => $this->url(),
            // 'fields' => array_map(fn ($field) => $field->jsonConfig(), $this->fields()),
        ];
    }
}
