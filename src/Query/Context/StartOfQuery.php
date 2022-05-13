<?php

namespace Pano\Query\Context;

class StartOfQuery extends QueryContext
{
    public function expects(): array
    {
        return [
            new ComparisonOperator(),
            new AndOperator(),
            new OrOperator(),
            new EndOfInput(),
            new Whitespace(),
        ];
    }

    protected function matches(string $query): false|int
    {
        return str_starts_with($query, '(');
    }

    protected function make(string $query): QueryContext
    {
        $this->characters = '(';

        return new StartOfQuery();
    }
}
