<?php

namespace Pano\Query\Directives;

use Elastico\Query\Joining\Nested;

class GroupByDirective extends PatternDirective
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

    public function directives(): array
    {
    }

    public function query($value)
    {
        return Nested::make()->path($this->key)->query($value);
    }

    public function pattern(): string
    {
        return '/by /i';
    }

    public function endPattern(): string
    {
        return ' ';
    }

    public function suggest(): array
    {
        return $this->directives()->map(fn ($directive) => $directive->definition());
    }
}
