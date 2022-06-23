<?php

namespace Pano\Query;

use Closure;
use Elastico\Models\Builder\Builder;
use Pano\Query\Directives\BaseDirective;
use Pano\Query\Executors\BooleanQueryExecutor;

class SearchExecutor
{
    protected ?GroupDirective $groupDirective = null;

    public function __construct(
        protected Builder $builder,
        protected array $patternDirectives = [],
        protected ?BaseDirective $baseDirective = null,
        protected ?Closure $beforeApplying = null,
    ) {
    }

    public function search(string $query): SearchResults
    {
        $this->applyQueryToBuilder($query);

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

    public function suggest(string $query): array
    {
        $builder = $this->applyQueryToBuilder($query);
        $currentDirective = $builder->getCurrentDirective();

        if ($currentDirective) {
            return $currentDirective->suggest($this->builder, $builder->getCurrentValue());
        }

        $unparsed = ltrim($builder->getUnparsed());

        return collect($this->patternDirectives)
            ->map(fn ($d) => [
                'type' => $d->getType(),
                'text' => $d->getText(),
                'description' => $d->getDescription(),
                'index' => strlen($query) + strlen($d->getText()),
            ])
            ->filter(fn ($p) => $unparsed ? str_contains($p['text'], $unparsed) : true)
            ->sortByDesc(fn ($p) => str_starts_with($p['text'], $unparsed))
            ->values()
            ->all()
        ;
    }

    protected function applyQueryToBuilder(string $query)
    {
        return new BooleanQueryExecutor(
            string: $query,
            directives: $this->patternDirectives,
            builder: $this->builder,
            query: $this->builder->getQuery(),
            end: ')',
        );
    }
}
