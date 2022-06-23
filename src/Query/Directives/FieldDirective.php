<?php

namespace Pano\Query\Directives;

use Elastico\Query\Query;
use Elastico\Query\Term\Prefix;
use Elastico\Query\Term\Term;

class FieldDirective extends PatternDirective
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
        return $this->key.':';
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
            new TokenValue(),
            new PhraseValue(),
            new BoolValues($this),
        ];
    }

    public function query($value): Query
    {
        if (str_ends_with($value, '*')) {
            return Prefix::make()->field($this->key)->value(substr($value, 0, -1));
        }

        return Term::make()->field($this->key)->value($value);
    }

    public function startPattern(): string
    {
        return "/{$this->key}:/i";
    }

    public function endPattern(): string
    {
        return '/\s+/';
    }

    public function suggest($builder, $query): array
    {
        return $builder->enumerate($this->key, string : $query, size: 10)
            ->take(20)
            ->filter()
            // ->filter(fn ($term) => str_starts_with(strtolower($term), strtolower($query)))
            ->map(fn ($e) => [
                'type' => 'term',
                'text' => $e,
            ])
            ->values()
            ->all()
        ;
    }
}
