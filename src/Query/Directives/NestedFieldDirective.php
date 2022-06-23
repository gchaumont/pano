<?php

namespace Pano\Query\Directives;

use Elastico\Query\Joining\Nested;
use Elastico\Query\Query;

class NestedFieldDirective extends PatternDirective
{
    public function __construct(protected string $key, public array $directives = [])
    {
        // code...
    }

    public function getText()
    {
        return "{$this->key}:{}";
    }

    public function getDescription()
    {
        return 'Filter results that contain '.$this->key;
    }

    public function getType(): string
    {
        return 'nested';
    }

    public function directives(): array
    {
        return $this->directives;
    }

    public function query($value): Query
    {
        return Nested::make()->path($this->key)->query($value);
    }

    public function startPattern(): string
    {
        return "/{$this->key}:{/i";
    }

    public function endPattern(): string
    {
        return '}';
    }

    public function suggest(): array
    {
        return collect($this->directives())->map(fn ($d) => [
            'type' => $d->getType(),
            'text' => $d->getText(),
            'description' => $d->getDescription(),
            'index' => 5,
        ])->all();
    }
}
