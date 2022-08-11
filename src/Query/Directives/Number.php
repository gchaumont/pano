<?php

namespace Pano\Query\Directives;

use Elastico\Query\Query;
use Elastico\Query\Term\Prefix;
use Elastico\Query\Term\Term;

class Number extends PatternDirective
{
    public function getType(): string
    {
        return 'field';
    }

    public function complete($builder): iterable
    {
        return [];
    }

    public function getAlias(): array
    {
        // code...
    }

    public function getDescription()
    {
        return 'Filter results that contain "'.$this->field.'"';
    }

    // public function query($value): Query
    // {
    //     if (str_ends_with($value, '*')) {
    //         return Prefix::make()->field($this->field)->value(substr($value, 0, -1));
    //     }

    //     return Term::make()->field($this->field)->value($value);
    // }

    public function isComplete(): bool
    {
        return true;
    }

    public function pattern(): string
    {
        return '/\d+/i';
    }
}
