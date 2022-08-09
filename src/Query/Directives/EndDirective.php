<?php

namespace Pano\Query\Directives;

class EndDirective extends PatternDirective
{
    public function __construct(protected string $pattern)
    {
        // code...
    }

    public function getType(): string
    {
        return '';
    }

    public function getText(): string
    {
        return ')';
    }

    public function getDescription(): string
    {
        return 'Close the current parenthesis';
    }

    public function pattern(): string
    {
        return $this->pattern;
    }

    public function isComplete(): bool
    {
        return true;
    }
}
