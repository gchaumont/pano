<?php

namespace Pano\Query\Directives;

use Elastico\Query\Term\Term;

class NumericFieldDirective extends FieldDirective
{
    public function __construct(protected string $key)
    {
        // code...
    }

    public function getType(): string
    {
        return 'field';
    }

    public function getText()
    {
        return $this->key;
    }

    public function getAlias(): array
    {
        // code...
    }

    public function getDescription()
    {
        return 'Filter results that contain "'.$this->key.'"';
    }

    public function directives(): array
    {
        return [
            new ExistsDirective(field: $this->key),
            new ContainsDirective(field: $this->key, values: [new Number()]),
            new CompareDirective(field: $this->key, values: [new Number()]),
        ];
    }

    public function pattern(): string
    {
        return "/{$this->key}/i";
    }
    // public function suggest($builder, $query): array
    // {
    //     return $builder->enumerate($this->key, string : $query, size: 10)
    //         ->take(20)
    //         ->filter()
    //         // ->filter(fn ($term) => str_starts_with(strtolower($term), strtolower($query)))
    //         ->map(fn ($e) => [
    //             'type' => 'term',
    //             'text' => $e,
    //         ])
    //         ->values()
    //         ->all()
    //     ;
    // }
}
