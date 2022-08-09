<?php

namespace Pano\Query\Directives;

use Elastico\Query\Query;
use Elastico\Query\Term\Prefix;
use Elastico\Query\Term\Term;

class Phrase extends PatternDirective
{
    public function getType(): string
    {
        return 'field';
    }

    public function getAlias(): array
    {
        // code...
    }

    public function getDescription()
    {
        return 'Filter results that contain "'.$this->field.'"';
    }

    public function directives(): array
    {
        return $this->values;
    }

    public function query($value): Query
    {
        if (str_ends_with($value, '*')) {
            return Prefix::make()->field($this->key)->value(substr($value, 0, -1));
        }

        return Term::make()->field($this->key)->value($value);
    }

    public function pattern(): string
    {
        return '/"([\w\s]+)"/i';
    }

    public function isComplete(): bool
    {
        return true;
    }

    // public function suggest($builder, $query): array
    // {
    //     return $builder->enumerate($this->key, string : $query, size: 10)
    //         ->take(20)
    //         ->filter()
    //         // ->filter(fn ($term) => str_starts_with(strtolower($term), strtolower($query)))
    //         ->map(fn ($e) => [
    //             'type' => 'term',
    //             'text' => $e,
    //         ])
    //         ->values()
    //         ->all()
    //     ;
    // }
}
