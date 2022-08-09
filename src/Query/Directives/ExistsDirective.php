<?php

namespace Pano\Query\Directives;

use Elastico\Query\Query;
use Elastico\Query\Term\Exists;

class ExistsDirective extends PatternDirective
{
    public function __construct(protected string $field)
    {
        // code...
    }

    public function getType(): string
    {
        return 'field';
    }

    public function getText()
    {
        return ':*';
    }

    public function getAlias(): array
    {
        // code...
    }

    public function getDescription()
    {
        return 'Filter results containing any "'.$this->field.'"';
    }

    public function query($value): Query
    {
        return Exists::make()->field($this->field);
    }

    public function pattern(): string
    {
        return '/:\\s+\\*/i';
    }
}
