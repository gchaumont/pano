<?php

namespace Pano\Query\Directives;

class OrDirective extends PatternDirective
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function getText()
    {
        return 'or';
    }

    public function getDescription()
    {
        return 'Requires one or more arguments to be true';
    }

    public function pattern(): string
    {
        return '/or/i';
    }
}
