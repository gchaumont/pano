<?php

namespace Pano\Query\Directives;

use Elastico\Query\Builder;

abstract class BaseDirective
{
    protected bool $useSuggestions = false;

    abstract public function apply(Builder $builder, string $value): void;

    public function compile(): static
    {
        // code...
    }

    /**
     * @return \Spatie\ElasticsearchStringParser\Suggestion[]
     */
    public function transformToSuggestions(array $results): array
    {
        return [];
    }

    public function canApply(string $value): bool
    {
        return true;
    }

    public function withSuggestions(): self
    {
        $this->useSuggestions = true;

        return $this;
    }
}
