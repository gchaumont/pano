<?php

namespace Pano\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Pano\Components\Component;
use Pano\Concerns\Nameable;
use Pano\Concerns\Routable;
use Pano\Controllers\ResourceController;
use Pano\Metrics\Results\MetricResult;

/**
 *  Metric.
 */
abstract class Metric extends Component
{
    use Nameable;
    use Routable;

    public string $component = 'Metric';

    public int $precision = 2;

    abstract public function calculate($request, Builder $builder = null): MetricResult;

    public function asJson($request, Builder $builder = null): array
    {
        return $this->calculate($request, $builder)->toJson();
    }

    public function getId(): string
    {
        return $this->id ??= 'metrics-'.Str::plural(Str::slug(class_basename(static::class)));
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

    public function url(): string
    {
        return route($this->getContext()->getLocation().'.'.$this->getId());
    }

    public function registerRoute(): void
    {
        Route::get($this->getPath(), [ResourceController::class, 'metric'])->name($this->getContextSeparator().$this->getRoute());
    }

    public function config(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'key' => $this->getId(),
            'path' => $this->getPath(),
            'url' => $this->url(),
        ];
    }

    protected function getType(): string
    {
        return static::TYPE;
    }
}
