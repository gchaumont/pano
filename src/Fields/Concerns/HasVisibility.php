<?php

namespace Pano\Fields\Concerns;

use Closure;

trait HasVisibility
{
    protected bool|Closure $visibleOnIndex = true;
    protected bool|Closure $visibleOnDetail = true;
    protected bool|Closure $visibleOnCreating = true;
    protected bool|Closure $visibleOnUpdating = true;

    public function showOnIndex(callable|bool $condition = true): static
    {
        $this->visibleOnIndex = $condition;

        return $this;
    }

    public function showOnDetail(callable|bool $condition = true): static
    {
        $this->visibleOnDetail = $condition;

        return $this;
    }

    public function showOnCreating(callable|bool $condition = true): static
    {
        $this->visibleOnCreate = $condition;

        return $this;
    }

    public function showOnUpdating(callable|bool $condition = true): static
    {
        $this->visibleOnUpdate = $condition;

        return $this;
    }

    public function hideFromIndex(callable|bool $condition = true): static
    {
        $this->showOnIndex(fn ($request) => is_callable($condition) ? !$condition($request) : !$condition);

        return $this;
    }

    public function hideFromDetail(callable|bool $condition = true): static
    {
        $this->showOnDetail(fn ($request) => is_callable($condition) ? !$condition($request) : !$condition);

        return $this;
    }

    public function hideWhenCreating(callable|bool $condition = true): static
    {
        $this->showOnCreating(fn ($request) => is_callable($condition) ? !$condition($request) : !$condition);

        return $this;
    }

    public function hideWhenUpdating(callable|bool $condition = true): static
    {
        $this->showOnUpdating(fn ($request) => is_callable($condition) ? !$condition($request) : !$condition);

        return $this;
    }

    public function onlyOnIndex(): static
    {
        return $this->showOnIndex(true)
            ->showOnDetail(false)
            ->showOnCreating(false)
            ->showOnUpdating(false)
        ;
    }

    public function onlyOnDetail(): static
    {
        return $this->showOnIndex(false)
            ->showOnDetail(true)
            ->showOnCreating(false)
            ->showOnUpdating(false)
        ;
    }

    public function onlyOnForms(): static
    {
        return $this->showOnIndex(false)
            ->showOnDetail(false)
            ->showOnCreating(true)
            ->showOnUpdating(true)
        ;
    }

    public function exceptOnForms(): static
    {
        return $this->showOnIndex(true)
            ->showOnDetail(true)
            ->showOnCreating(false)
            ->showOnUpdating(false)
        ;
    }

    public function isVisibleOnDetail($request): bool
    {
        return is_callable($this->visibleOnDetail) ? call_user_func($this->visibleOnDetail, $request) : $this->visibleOnDetail;
    }

    public function isVisibleOnIndex($request): bool
    {
        return is_callable($this->visibleOnIndex) ? call_user_func($this->visibleOnIndex, $request) : $this->visibleOnIndex;
    }

    public function isVisibleOnCreate($request): bool
    {
        return is_callable($this->visibleOnCreate) ? call_user_func($this->visibleOnCreate, $request) : $this->visibleOnCreate;
    }

    public function isVisibleOnUpdate($request): bool
    {
        return is_callable($this->visibleOnUpdate) ? call_user_func($this->visibleOnUpdate, $request) : $this->visibleOnUpdate;
    }
}
