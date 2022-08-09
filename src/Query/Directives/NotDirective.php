<?php

namespace Pano\Query\Directives;

class NotDirective extends PatternDirective
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function getText()
    {
        return 'not';
    }

    public function getDescription()
    {
        return 'Requires the argument to be negative';
    }

    public function pattern(): string
    {
        return '/not/i';
    }
}
