<?php

namespace Pano\Query\Context;

class ComparisonOperator extends QueryContext
{
    protected static $operators = ['<=', '>=', '<',  '>', ':'];

    public function expects(): array
    {
        return [
            new OpenGroup(),
            new OpenNested(),
            new Value(),
            new Whitespace(),
        ];
    }

    protected function matches(string $query): false|int
    {
        foreach (static::$operators as $operator) {
            if (str_starts_with($query, $operator)) {
                return strlen($operator);
            }
        }

        return false;
    }

    protected function make(string $query): QueryContext
    {
        $this->characters = '(';

        return new StartOfQuery();
    }
}
