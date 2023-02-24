<?php

namespace Pano\Fields\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Pano\Query\Directives\Directive;

trait HasFilters
{
    protected \Closure|bool $filterable = false;

    protected null|Directive $directive = null;

    public function filterable(bool|callable $filterable = true): static
    {
        $this->filterable = $filterable;

        return $this;
    }

    public function applyFilter($request, Builder $query, mixed $value): Builder
    {
        if (is_callable($this->filterable)) {
            return call_user_func($this->filterable, func_get_args());
        }

        if (str_contains($value, ',')) {
            return $query->whereIn($this->field(), explode(',', $value));
        }

        return $query->where($this->field(), $value);
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
