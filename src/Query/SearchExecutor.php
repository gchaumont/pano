<?php

namespace Pano\Query;

use Closure;
use Elastico\Models\Builder\Builder;
use Pano\Query\Directives\BaseDirective;
use Pano\Query\Directives\BooleanDirective;
use Pano\Query\Directives\Directive;

class SearchExecutor
{
    protected ?GroupDirective $groupDirective = null;

    protected null|Directive $currentDirective = null;

    protected Directive $tree;

    public function __construct(
        protected Builder $builder,
        protected array $patternDirectives = [],
        protected ?BaseDirective $baseDirective = null,
        protected ?Closure $beforeApplying = null,
    ) {
    }

    public function setCurrentDirective(Directive $directive): static
    {
        $this->currentDirective = $directive;

        return $this;
    }

    public function search(string $query): SearchResults
    {
        $this->buildSyntaxTree($query);

        $this->builder = $this->tree->compile($this->builder);
        if (!request()->expectsJson()) {
            dump($query);
            dump($this->tree);
            $this->builder->dd();
        }

        if ($this->groupDirective) {
            $this->builder->size(0);
            $this->builder->from(0);
        }

        $results = $this->builder->get();

        $hits = $this->groupDirective
            ? $this->groupDirective->transformToHits($results)
            : $results->hits()->all();

        return new SearchResults(
            $hits,
            $suggestions ?? [],
            null !== $this->groupDirective,
            $results->response()
        );
    }

    public function suggest(string $query, int $index): array
    {
        $this->buildSyntaxTree($query);

        $currentDirective = $this->tree->directiveForIndex($index);

        $r = collect($currentDirective->suggest($this->builder))
            ->map(function ($directive) use ($currentDirective) {
                $directive['start'] ??= $currentDirective->_debug['index'] + $currentDirective->_debug['length'];
                $directive['end'] ??= $currentDirective->_debug['index'] + $currentDirective->_debug['length'] + strlen($currentDirective->_debug['internal_rest']);

                return $directive;
            })
            ->values()
            ->all()
        ;
        if (!request()->expectsJson()) {
            dd([
                'query' => $query,
                'index' => $index,
                'tree' => $this->tree,
                'directiveForIndex' => $this->tree->directiveForIndex($index),
                'directives' => $this->tree->directiveForIndex($index)->directives(),
                'suggestions' => $r,
                'builder' => $this->tree->compile($this->builder),
            ]);
        }

        return $r;
    }

    public function getCurrentDirective(): null|Directive
    {
        return $this->currentDirective;
    }

    public function buildSyntaxTree(string $query): void
    {
        $this->tree = (new BooleanDirective(
            directives: $this->patternDirectives,
            start: null,
            end: null
        ));

        $this->tree->build($query);
    }
}
