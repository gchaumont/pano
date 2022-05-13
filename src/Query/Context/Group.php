<?php

namespace Pano\Query\Context;

class Group extends QueryContext
{
    public function expects(): array
    {
        return [
            new Group(),
            new Not(),
            new EndOfInput(),
            new FieldName(),
            new Value(),
        ];
    }

    protected function matches(string $query): int|false
    {
        return str_starts_with($query, '(');
    }

    protected function make(string $query): QueryContext
    {
        $this->characters = '(';

        return new StartOfQuery();
    }
}
