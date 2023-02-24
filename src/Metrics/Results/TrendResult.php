<?php

namespace Pano\Metrics\Results;

/**
 *  Metric.
 */
class TrendResult extends MetricResult
{
    protected array $trend;

    public function trend(array $trend): static
    {
        $this->trend = array_values($trend);
        $this->labels = array_keys($trend);

        return $this;
    }

    public function transform(callable $transform): static
    {
        foreach ($this->trend as &$trend) {
            $trend = $transform($trend);
        }

        return $this;
    }

    public function transformLabels(callable $transform): static
    {
        foreach ($this->label as &$label) {
            $label = $transform($label);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            // 'labels' => $this->getSpacedLabels($this->labels),
            'labels' => $this->labels,
            'trend' => $this->trend,
            'suffix' => $this->suffix,
            'prefix' => $this->prefix,
        ];
    }

    public function getSpacedLabels(array $labels): array
    {
        if (count($labels) > 10) {
            $every = round(count($labels) / 10);
            foreach ($labels as $i => &$label) {
                if (0 !== $i % $every) {
                    $label = null;
                }
            }
        }

        return $labels;
    }
}
