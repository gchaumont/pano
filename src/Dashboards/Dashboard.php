<?php

namespace Pano\Dashboards;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Concerns\Nameable;
use Pano\Metrics\Metric;
use Pano\Pages\Page;

abstract class Dashboard extends Page
{
    use Nameable;

    public string $icon;

    public string $component = 'Dashboard';

    // const CONTEXT_SEPARATOR = ':';

    // public function getId(): string
    // {
    //     return 'dashboards:'.$this->id;
    // }

    public function getIcon(): ?string
    {
        return isset($this->icon) ? $this->icon.'-icon' : null;
    }

    public function metrics(): array
    {
        return [];
    }

    // public function getRoute(): string
    // {
    //     return implode(':dashboards.', [$this->getNamespace(), $this->getRouteKey()]);
    // }

    public function getMetrics(): Collection
    {
        return $this->metrics ??= collect($this->metrics())->keyBy(fn ($m) => $m->getKey());
    }

    public function getMetric(string $metric): Metric
    {
        $metric = $this->getMetrics()->get($metric);

        if (empty($metric)) {
            throw new \RuntimeException("The metric [{$metric}] was not found in the Resource ".$this->getName());
        }

        return $metric;
    }

    // public function getMetrics(): array
    // {
    //     return $this->metrics ??= collect($this->metrics())->map(fn ($m) => $m->register($this->getContext()))->all();
    // }

    public function getChildren(): Collection
    {
        return $this->getMetrics();
    }

    // public function getRoute(): string
    // {
    //     return $this->route ?? Str::slug($this->getPath());

    //     return 'dashboards.'.($this->route ?? Str::slug($this->getPath()));
    // }

    public function getProps(): array
    {
        return [
            'name' => $this->getName(),
            'metrics' => $this->getMetrics()->map(fn ($m) => $m->config())->values(),
            // 'route' => $this->getRoute(),
            // 'breadcrumbs' => $this->getBreadcrumbs(),
            // 'route' => $this->getLocation(),
            // 'path' => $this->url(),
            'icon' => $this->getIcon(),
        ];
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            '@type' => 'Dashboard',
        ];
    }
}
