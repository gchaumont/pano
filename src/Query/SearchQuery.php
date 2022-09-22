<?php

namespace Pano\Query;

use Elastico\Query\Builder;
use Pano\Query\Directives\PatternDirective;

class SearchQuery
{
    // use ForwardsCalls;

    protected array $patternDirectives = [];

    protected ?BaseDirective $baseDirective = null;

    protected Builder $builder;

    protected ?GroupDirective $groupDirective = null;

    protected ?Closure $beforeApplying = null;

    public function __construct(
        Builder $builder
    ) {
        $this->builder = $builder;
    }

    public function __call(string $method, array $arguments)
    {
        $this->forwardCallTo($this->builder, $method, $arguments);

        return $this;
    }

    /**
     * This directive will be applied to the remainder of the search query
     * after all other directive have been applied and removed from the
     * search string.
     *
     * @param \Spatie\ElasticsearchStringParser\Directives\BaseDirective $filter
     *
     * @return $this
     */
    public function baseDirective(BaseDirective $filter): static
    {
        $this->baseDirective = $filter;

        return $this;
    }

    public function patternDirectives(PatternDirective ...$patternDirectives): static
    {
        $this->patternDirectives = $patternDirectives;

        return $this;
    }

    public function beforeApplying(Closure $closure): static
    {
        $this->beforeApplying = $closure;

        return $this;
    }

    public function search(string $query): SearchResults
    {
        return $this->getSearchExecutor()->search($query);
    }

    public function suggest(string $query, int $index): array
    {
        return $this->getSearchExecutor()->suggest($query, $index);
    }

    public function applyQueryToBuilder(string $query): Builder
    {
        return $this->getSearchExecutor()->applyToBuilder($query);
    }

    public function getSearchExecutor(): SearchExecutor
    {
        // Syntax Tree
        return new SearchExecutor(
            clone $this->builder,
            $this->patternDirectives,
            $this->baseDirective,
            $this->beforeApplying,
        );
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }
}
