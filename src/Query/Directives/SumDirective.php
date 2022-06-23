<?php

namespace Pano\Query\Directives;

use Elastico\Aggregations\Metric\Sum;

class SumDirective extends PatternDirective
{
    public function __construct(protected string $key)
    {
        // code...
    }

    public function expects(): array
    {
        return [
            new FieldNameValue(),
        ];
    }

    public function aggregation($value)
    {
        return (new Sum('sum'))->field($value);
    }

    public function startPattern(): string
    {
        return '/sum(/i';
    }

    public function endPattern(): string
    {
        return ')';
    }

    public function suggest(): array
    {
        return $this->directives()->map(fn ($directive) => $directive->definition());
    }
}
