<?php

namespace Pano\Resource;

use Elastico\Models\DataAccessObject;
use Elastico\Models\Model;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Pano\Concerns\Linkable;
use Pano\Fields\Groups\Stack;
use Pano\Metrics\Metric;

abstract class Resource
{
    use Linkable;

    public string $group;

    public null|string $icon = null;

    public static string $title = 'name';
    /**
     * Columns that should be searched.
     */
    public array $search = ['id'];

    public string $tableStyle = 'tight';

    // Polling
    public bool $polling = true;

    // Polling seconds
    public int $pollingInterval = 5;

    // Search
    public $debounce = 0.5; // 0.5 seconds

    public $showPollingToggle = true;

    public bool $showColumnBorders = true;

    // Eager load relations
    public array $with = [];

    protected string $model;

    protected int $perPage = 50;

    public function getName(): string
    {
        return Str::plural(Str::headline(class_basename($this->model)));
    }

    public function getTitle(DataAccessObject $object): string
    {
        return $object->{static::$title} ?? $object->get_id();
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

    public function linkTo(string|Model $resource): string
    {
        $resource = $resource instanceof Model ? $resource->get_id() : $resource;

        return route($this->getRoute('show'), $resource, false);
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
        return $this->getFields()->first(fn ($f) => $f->getKey() == $relation);
    }

    public function getFields(): BaseCollection
    {
        return collect($this->fields());
    }

    public function getMetrics(): array
    {
        return $this->metrics ??= collect($this->metrics())->map(fn ($m) => $m->namespace($this->getRoute()))->all();
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
