<?php

namespace Pano\Fields\Concerns;

use Illuminate\Support\Collection;

trait HasData
{
    public array|\Closure $data;

    public function getData($request): Collection
    {
        $data = $this->data ?? $this->data($request);

        return collect(is_callable($data) ? call_user_func($data) : $data);
    }

    public function data($request): array
    {
        return [];
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
