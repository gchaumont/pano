<?php

namespace Pano\Query\Directives;

use Elastico\Query\Compound\Boolean;
use Elastico\Query\Joining\Nested;

class NestedFieldDirective extends BooleanDirective
{
    public function __construct(protected string $key, public array $directives = [])
    {
        parent::__construct(
            directives: $directives,
            nested: false,
            start: '{',
            end: '}',
            allow_single: false
        );

        foreach ($this->directives as $directive) {
            $directive->path($this->key);
        }
    }

    public function initialise(): void
    {
        if (empty($this->baseQuery)) {
            $this->baseQuery = new Boolean();
            $nestedQuery = (new Nested())->path($this->key)->query($this->baseQuery);

            $this->getCurrentQuery()->filter($nestedQuery);
            $this->setCurrentQuery(new Boolean());
            $this->baseQuery->should($this->getCurrentQuery());
        }
    }

    public function getText()
    {
        return "{$this->key}:{ ";
    }

    public function getDescription()
    {
        return 'Filter results that contain '.$this->key;
    }

    public function getType(): string
    {
        return 'nested';
    }

    public function pattern(): string
    {
        return "/{$this->key}:{/i";
    }
}
