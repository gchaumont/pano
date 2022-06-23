<?php

namespace Pano\Query\Directives;

use Elastico\Query\FullText\MatchQuery;
use Elastico\Query\Query;
use Elastico\Query\Term\Term;
use Pano\Query\Support\Regex;

abstract class Directive
{
    protected bool $acceptsBooleanLogic = true;

    abstract public function startPattern(): string;

    public function handle(string $input): Query
    {
        return $this->acceptsBooleanLogic
            ? $this->handleBool($this, $input)
            : $this->handleSingle($this, $input);
    }

    public function appliesTo($query): bool
    {
        $count = Regex::mb_preg_match($this->startPattern(), $query, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        return ($matches[0][1] ?? null) === 0;
        if (0 === ($matches[1] ?? null)) {
            return true;
        }

        return (bool) $count;
        $value = trim($matches[0][0]);
        $index = $matches[0][1];

        // $this->buildQuery($value, '', fn ($query) => (new MatchQuery())->field($this->key)->query($query));

        // $parsed = $this->parseValue($value);
        // dd($matches);

        return !empty($matches);
    }

    public function handleBool($directive, $input)
    {
        return $directive->query($input);
    }

    public function handleSingle($directive, $input)
    {
        foreach ($directive->expects() as $expectation) {
            if ($expectation->matches($input)) {
                return $expectation->handle($input);
            }
        }
    }

    public function expects(): array
    {
        return [];
    }

    public function query($value): Query
    {
        return Term::make()->field($this->key)->value($value);
    }

    public function endPattern(): string
    {
        return '/\s+/';
    }

    // public function suggest(): array
    // {
    //     return $this->directives()->map(fn ($directive) => $directive->definition());
    // }
}
