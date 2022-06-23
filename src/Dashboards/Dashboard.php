<?php

namespace Pano\Dashboards;

use Pano\Concerns\Linkable;
use Pano\Metrics\Metric;

abstract class Dashboard
{
    use Linkable;

    public function metrics(): array
    {
        return [];
    }

    public function getRoute(): string
    {
        return implode(':dashboards.', [$this->getNamespace(), $this->getRouteKey()]);
    }

    public function getMetric(string $metric): Metric
    {
        $metric = collect($this->getMetrics())->first(fn ($m) => $m->getRouteKey() == $metric);
        if (empty($metric)) {
            throw new \RuntimeException("The metric [{$metric}] was not found in the Resource ".$this->getName());
        }

        return $metric;
    }

    public function getMetrics(): array
    {
        return $this->metrics ??= collect($this->metrics())->map(fn ($m) => $m->namespace($this->getRoute()))->all();
    }

    public function jsonConfig(): array
    {
        return [
            'name' => $this->getName(),
            'metrics' => array_map(fn ($metric) => $metric->jsonConfig(), $this->getMetrics()),
            'route' => $this->getRoute(),
            'path' => $this->url(),
        ];
    }
}
