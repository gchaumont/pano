<?php

namespace Pano\Query\Directives;

class Whitespace extends Directive
{
    public function getType(): string
    {
        return 'whitespace';
    }

    public function getAlias(): array
    {
        // code...
    }

    public function getDescription()
    {
        return '';
    }

    public function pattern(): string
    {
        return '/\s+/i';
    }

    public function suggest($builder): array
    {
        return $this->parent->suggest($builder);
    }
}
