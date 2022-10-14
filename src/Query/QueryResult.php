<?php

namespace Pano\Query;

use Closure;

class QueryResult
{
    public function __construct(
        public array $hits,
        public int $total,
    ) {
    }

    public function hits()
    {
        return collect($this->hits);
    }

    public function all(): array
    {
        return $this->hits;
    }

    public function first(): ?object
    {
        return collect($this->hits)->first();
    }

    public function map(Closure $callback)
    {
        $this->hits = collect($this->hits)->map($callback)->all();

        return $this;
    }

    public function total()
    {
        return $this->total;
    }
}
