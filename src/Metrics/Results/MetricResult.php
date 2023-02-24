<?php

namespace Pano\Metrics\Results;

use Illuminate\Contracts\Support\Arrayable;

/**
 *  Metric.
 */
abstract class MetricResult implements Arrayable
{
    protected int|float|array $result;

    protected int|float|array $previous;

    protected string|null $prefix = null;

    protected string|null $suffix = null;

    protected string $currency;

    abstract public function toArray(): array;

    public function previous(int|float|array $previous): static
    {
        $this->previous = $previous;

        return $this;
    }

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function currency($currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}
