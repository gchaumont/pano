<?php

namespace Pano\Query\Directives;

class AndDirective extends PatternDirective
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function getText()
    {
        return 'and';
    }

    public function getDescription()
    {
        return 'Requires both arguments to be true';
    }

    public function pattern(): string
    {
        return '/and/i';
    }
}
