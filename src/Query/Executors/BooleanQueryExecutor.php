<?php

namespace Pano\Query\Executors;

use Closure;
use Elastico\Query\Builder;
use Elastico\Query\Query;
use Pano\Query\Directives\Directive;
use RuntimeException;

class BooleanQueryExecutor
{
    protected null|string $unparsedQuery;

    protected null|Directive $currentDirective = null;

    protected null|string $currentValue = null;

    protected bool $boolState = true;

    public function __construct(
        protected string $string,
        protected array $directives,
        protected Builder $builder,
        protected Query $query,
        protected string|Closure $end,
        protected null|Directive $baseDirective = null,
    ) {
        $this->handle();
    }

    public function handle()
    {
        $this->unparsedQuery = $this->string;

        while ($this->unparsedQuery) {
            $this->unparsedQuery = ltrim($this->unparsedQuery);
            if ($this->handleBooleanEnd()) {
                break;
            }
            if ($this->handleBooleanOperators()) {
                continue;
            }
            if ($this->handlePatternDirectives()) {
                continue;
            }
            if ($this->handleBaseDirective()) {
                continue;
            }

            // throw new RuntimeException('No applicable directive for string: ['.$this->unparsedQuery.']');
            break;
        }
    }

    public function getCurrentDirective(): null|Directive
    {
        return $this->currentDirective;
    }

    public function getCurrentValue(): null|string
    {
        return $this->currentValue;
    }

    public function getUnparsed(): null|string
    {
        return $this->unparsedQuery;
    }

    public function handlePatternDirectives(): bool
    {
        foreach ($this->directives as $directive) {
            if ($directive->appliesTo($this->unparsedQuery)) {
                $this->applyDirective($directive, $this->unparsedQuery);

                return true;
            }
        }

        return false;
    }

    public function handleBooleanEnd(): bool
    {
        return is_callable($this->end)
                ? ($this->end)($this->unparsedQuery)
                : str_starts_with($this->unparsedQuery, $this->end);
    }

    public function handleBaseDirective(): bool
    {
        if (null === $this->baseDirective) {
            return false;
        }

        $this->applyDirective($this->baseDirective, $this->unparsedQuery);

        return true;
    }

    protected function parseValue(string $value): string
    {
        if (str_starts_with($value, '(')) {
            new BooleanQueryExecutor(
                $value,
                $this->currentDirective->directives,
                $this->builder,
            );
        }

        $parts = explode(' ', $value, 2);

        $this->unparsedQuery = $parts[1] ?? null;

        return $parts[0];
    }

    protected function applyDirective(Directive $directive, string $query): string
    {
        $this->currentDirective = $directive;

        $rest = preg_replace($directive->startPattern(), '', $query, 1);
        $rest = ltrim($rest);

        // parse value
        $value = $this->parseValue($rest);

        $this->currentValue = $value;

        // response([$query, $directive->startPattern(), $value, $rest])->send();
        if (null !== $this->getUnparsed() && !empty($value)) {
            $this->currentDirective = null;
        }
        // pass parsed value to directive->query();
        $query = $directive->handle($value, $this->builder);

        $this->boolState ? $this->query->must($query) : $this->query->mustNot($query);

        return $rest;
    }

    protected function handleBooleanOperators(): bool
    {
        if (str_starts_with($this->unparsedQuery, 'NOT')) {
            $this->boolNegative = true;
            $this->unparsedQuery = substr($this->unparsedQuery, 3);

            return true;
        }
        if (str_starts_with($this->unparsedQuery, 'AND')) {
            $this->unparsedQuery = substr($this->unparsedQuery, 3);

            return true;
            // Implicit
        }
        if (str_starts_with($this->unparsedQuery, 'OR')) {
            $this->unparsedQuery = substr($this->unparsedQuery, 2);

            $this->currentBool = new Boolean();
            $this->builder->should($this->currentBool);

            return true;
        }

        return false;
    }
}
