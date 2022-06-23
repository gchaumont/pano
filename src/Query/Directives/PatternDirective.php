<?php

namespace Pano\Query\Directives;

use Elastico\Query\Builder;

abstract class PatternDirective extends Directive
{
    public array $directives = [];

    // abstract public function apply(Builder $builder, string $pattern, array $values, int $patternOffsetStart, int $patternOffsetEnd): void;

    // abstract public function startPattern(): string;

    // abstract public function endPattern(): string;

    public function canApply(string $pattern, array $values = []): bool
    {
        return true;
    }
}
