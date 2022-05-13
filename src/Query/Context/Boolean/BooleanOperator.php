<?php

namespace Pano\Query\Context\Boolean;

use Pano\Query\Context\QueryContext;

class BooleanOperator extends QueryContext
{
    public function expects(): array
    {
        return [
            new OpenParentheses(),
            new FieldName(),
            new Value(),
        ];
    }

    protected function matches(string $query): false|int
    {
        if (str_starts_with(strtoupper($query), static::$key)) {
            return strlen(static::$key);
        }

        return false;
    }
}
