<?php

namespace Pano\Query;

use Closure;

class SearchResults
{
    public function __construct(
        public array $hits,
        public array $suggestions,
        public bool $isGrouped,
        public array $raw,
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

    public function map(Closure $callback)
    {
        $this->hits = collect($this->hits)->map($callback)->all();

        return $this;
    }

    public function total()
    {
        return $this->raw['hits']['total']['value'];
    }
}
