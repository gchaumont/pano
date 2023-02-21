<?php

namespace Pano\Concerns;

use Illuminate\Support\Collection;

trait HasBreadcrumbs
{
    protected Collection $breadcrumbs;

    public function breadcrumbs(): array
    {
        return [];
    }

    public function setBreadcrumbs(array $breadcrumbs): static
    {
        $this->breadcrumbs = collect($breadcrumbs);

        return $this;
    }

    public function getBreadcrumbs(): Collection
    {
        return $this->breadcrumbs ??= collect($this->breadcrumbs());
    }
}
