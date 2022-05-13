<?php

namespace Pano\Query\Context;

class StartOfInput extends QueryContext
{
    public function expects(): array
    {
        return [
            new OpenParentheses(),
            new NotOperator(),
            new EndOfInput(),
            new FieldName(),
            new Value(),
        ];
    }

    protected function matches(string $query): false|int
    {
        return true;
    }
}
