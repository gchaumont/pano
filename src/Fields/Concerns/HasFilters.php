<?php

namespace Pano\Fields\Concerns;

use Closure;
use Pano\Query\Directives\Directive;

trait HasFilters
{
    protected Closure|bool $filterable = true;

    protected null|Directive $directive = null;

    public function filterable(bool|callable $filterable = true): static
    {
        $this->filterable = $filterable;

        return $this;
    }

    public function applyFilter($request, $query, $value, $attribute): Builder
    {
        return is_callable($this->filterable) ? call_user_func($this->filterable, func_get_args()) : $query;
    }

    public function isFilterable($request): bool
    {
        return !empty($this->filterable) && $this->filterable;
    }

    public function getDirective(): ?Directive
    {
        return $this->directive;
    }
}
