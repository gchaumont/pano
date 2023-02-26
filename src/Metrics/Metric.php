<?php

namespace Pano\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Pano\Components\Component;
use Pano\Concerns\Nameable;
use Pano\Concerns\Routable;
use Pano\Controllers\ResourceController;
use Pano\Metrics\Results\MetricResult;
use Pano\Resource\Resource;

/**
 *  Metric.
 */
abstract class Metric extends Component
{
    use Nameable;
    use Routable;

    public string $component = 'Metric';

    public int $precision = 2;

    public string $resource;

    public function __construct()
    {
        $this->key($this->getKey());
    }

    abstract public function calculate($request, Builder $builder): MetricResult;

    public function getResource(): Resource
    {
        return $this->getApplication()->getResource($this->resource);
    }

    public function cacheFor(): int|null|\DateTime
    {
        return -1;
    }

    public function precision(int $precision): static
    {
        $this->precision = $precision;

        return $this;
    }

    // public function url(): string
    // {
    //     return route($this->getLocation());
    // }

    public function registerRoute(): void
    {
        // Route::get($this->getPath(), [ResourceController::class, 'metric'])->name($this->getContextSeparator().$this->getKey());
    }

    public function data($request): array
    {
        return [
            'value' => fn ($request) => $this->calculate($request, $this->getResource()->newFilteredQuery($request)),
            // 'data' => Data::get()
            //     ->query(Data::inject('filters.'.$this->getResource()->key()))
            //     ->updateOn(Event::ui('resourceFiltered'))
            //     ->onFilter(Event::ui('resourceFiltered')),
        ];
    }

    public function getProps(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'key' => $this->getKey(),
            // 'path' => $this->getPath(),
        ];
    }

    public function getContextSeparator(): string
    {
        return '.';
    }

    protected function getType(): string
    {
        return static::TYPE;
    }
}
