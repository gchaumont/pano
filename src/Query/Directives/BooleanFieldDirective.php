<?php

namespace Pano\Query\Directives;

use Elastico\Query\Query;
use Elastico\Query\Term\Term;

class BooleanFieldDirective extends PatternDirective
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
        ];
    }

    public function query($value): Query
    {
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
        return collect([1, 0])
            ->map(fn ($e) => [
                'type' => 'term',
                'text' => $e,
            ])
            ->values()
            ->all()
        ;
    }
}
