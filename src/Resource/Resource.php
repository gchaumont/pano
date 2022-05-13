<?php

namespace Pano\Resource;

abstract class Resource
{
    public string $model;

    public string $group;

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

    public function name(): string
    {
        return class_basename($this->model);
    }

    public function uriKey(): string
    {
        return strtolower($this->name());
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

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey().'/'.$resource->getKey();
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey().'/'.$resource->getKey();
    }

    public static function redirectAfterDelete(NovaRequest $request)
    {
        return null;
    }

    public function fieldsForIndex($request): array
    {
    }

    public function fieldsForDetail($request): array
    {
    }

    public function fieldsForCreate($request): array
    {
    }

    public function fieldsForUpdate($request): array
    {
    }

    public function url(): string
    {
        // return route($app->getAppRoute().'.app.resource.index', [
        //     'app' => $app->uriKey(),
        //     'resource' => $this->uriKey(),
        // ], false);
    }

    public function jsonConfig(): array
    {
        return [
            'name' => $this->name(),
            'key' => $this->uriKey(),
            // 'apiKey' => $this->
            // 'link' => $this->url($app),
            'fields' => array_map(fn ($field) => $field->jsonConfig(), $this->fields()),
        ];
    }
}
