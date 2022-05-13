<?php

namespace Pano\Query\Context;

abstract class QueryContext
{
    protected string $characters = '';

    public function parse(string $query): static
    {
        $matches = [];
        foreach ($this->expects() as $context) {
            if ($context->matches($query)) {
                $matches[] = $context;
            }
        }
        if (1 == count($matches)) {
            return $context;
        }
    }

    abstract protected function matches(string $query): false|int;
}
