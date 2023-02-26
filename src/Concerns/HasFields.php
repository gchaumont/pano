<?php

namespace Pano\Concerns;

use Illuminate\Support\Collection;

trait HasFields
{
    protected Collection $fields;

    public function fields(): array
    {
        return [];
    }

    public function getFields(): Collection
    {
        return $this->fields ??= collect($this->fields());
    }
}
