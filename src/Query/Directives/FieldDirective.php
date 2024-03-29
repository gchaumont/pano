<?php

namespace Pano\Query\Directives;

use Elastico\Query\Term\Term;

class FieldDirective extends PatternDirective
{
    public null|string $path = null;

    public function __construct(protected string $key)
    {
        // code...
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
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
            new ContainsDirective(
                field: $this->key,
                path: $this->path,
                values: [
                    new TokenOrPhrase(field: $this->key, path: $this->path),
                ]
            ),
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
