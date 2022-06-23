<?php

namespace Pano\Query;

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

    public function total()
    {
        return $this->raw['hits']['total']['value'];
    }
}
