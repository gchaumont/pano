<?php

namespace Pano\Concerns;

use Illuminate\Support\Collection;

trait HasPages
{
    protected Collection $pages;

    public function pages(): array
    {
        return [];
    }

    public function setPages(array $pages): static
    {
        $this->pages = collect($pages);

        return $this;
    }

    public function getPages(): Collection
    {
        return $this->pages ??= collect($this->pages()); // ->keyBy(fn ($page) => $page->getId());
    }
}
