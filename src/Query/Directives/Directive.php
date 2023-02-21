<?php

namespace Pano\Query\Directives;

use Elastico\Query\Compound\Boolean;
use Elastico\Query\FullText\MatchQuery;
use Elastico\Query\Query;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Pano\Query\Support\Regex;

// Syntax Tree
abstract class Directive
{
    public bool $negatable = false;

    public array $_debug = [];
    // start
    // end
    // handler (returns last directive)
    // query
    // suggest

    protected Boolean $currentQuery;

    protected null|Directive $parent = null;

    protected Builder $root;

    protected string $match;

    protected array $nodes = [];

    abstract public function pattern(): string;

    public function handle(Directive $node, null|Directive $parent, mixed $root): void
    {
    }

    public function initialise(): void
    {
    }

    /**
     * Compile the syntax tree.
     */
    public function compile(object $root, Directive|null $parent = null): mixed
    {
        $this->root = $root;

        $this->initialise();

        foreach ($this->nodes as $node) {
            $node->compile($root, $this);

            $this->handle($node, $parent, $root);
        }

        return $root;
    }

    public function directives(): array
    {
        return [];
    }

    public function matches(string $query)
    {
        Regex::mb_preg_match($this->pattern(), $query, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        return $matches;
    }

    public function appliesTo($query): bool
    {
        // $query = ltrim($query);

        $count = Regex::mb_preg_match($this->pattern(), $query, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

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

    public function query($value): null|Query
    {
        return null;
    }

    public function suggest($builder): array
    {
        // if (1 == count($this->directives())) {
        //     return $this->directives()[0]->suggest($builder, $this->match);
        // }

        return collect($this->directives())
            ->flatMap(fn ($directive) => $directive->complete($builder))
            // ->filter(fn ($d) => str_contains($d['text'], $this->match))
            ->values()
            ->all()
        ;
    }

    public function complete($builder): iterable
    {
        return [$this->definition()];
    }

    public function definition(): array
    {
        return [
            'type' => $this->getType(),
            'text' => $this->getText(),
            'description' => $this->getDescription(),
            'start' => $this->_debug['index'] ?? null,
            'end' => $this->_debug['length'] ?? null,
        ];
    }

    public function getCurrentQuery(): Query|Builder|BaseBuilder
    {
        return $this->currentQuery ?? $this->parent?->getCurrentQuery() ?? $this->root->getQuery();
    }

    public function setCurrentQuery(Query $query): static
    {
        $this->currentQuery = $query;

        return $this;
    }

    /**
     * Build the syntax tree.
     *
     * @param mixed $index
     */
    public function build(string $input, int $index = 0): string
    {
        $rawInput = $input;
        // $input = ltrim($input);
        $rest = preg_replace($this->pattern(), '', $input, 1);
        preg_match($this->pattern(), $input, $matches);
        $this->match = $matches[0];
        // $this->match = substr($input, 0, strpos($input, $rest));

        $this->_debug = [
            'id' => spl_object_id($this),
            'match' => $this->match,
            'input' => $input,
            'rest' => $rest,
            'internal_rest' => $rest,
            'index' => $index,
            'length' => strlen($this->match),
            'pattern' => $this->pattern(),
        ];

        $rest = $this->buildChildren($rest, $index + $this->_debug['length']);

        $this->_debug['total_length'] = $this->_debug['length'] + collect($this->nodes)->sum(fn ($n) => $n->_debug['total_length']);
        $this->_debug['end_index'] = $this->_debug['index'] + $this->_debug['total_length'];

        return $rest;
    }

    public function buildChildren(string $input, int $index): string
    {
        while (!$this->isComplete()) {
            $directive = collect($this->directives())
                ->push(new Whitespace())
                ->first(fn ($d) => $d->appliesTo($input))
            ;

            if (!$directive) {
                // throw new Exception('Parsing Error for : '.$input);

                break;
            }
            $len = collect($this->nodes)->sum(fn ($n) => $n->_debug['total_length']);

            $this->nodes[] = $directive = clone $directive;

            $directive->parent = $this;

            $input = $directive->build($input, $index + $len);

            $this->_debug['internal_rest'] = $input;
        }

        return $input;
    }

    /**
     * The directive doesn't accept new input.
     */
    public function isComplete(): bool
    {
        return empty($this->directives()) || collect($this->nodes)->contains(fn ($n) => !($n instanceof Whitespace));
    }

    public function last(): self
    {
        return collect($this->nodes)->last() ?? $this;
    }

    public function directiveForIndex(int $index): self
    {
        return collect($this->nodes)
            ->filter(fn ($directive) => $directive->_debug['index'] <= $index)
            ->filter(fn ($directive) => $directive->_debug['end_index'] >= $index)
            ->last()
            ?->directiveForIndex($index)
            ?? $this;
    }
}
