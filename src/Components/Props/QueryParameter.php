<?php

namespace Pano\Components\Props;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Part of the User Interface.
 */
class QueryParameter implements Arrayable
{
    public string $name;

    public function __construct(
        public string $parameter,
        string $name = null
    ) {
        $this->parameter = $parameter;
        $this->name = $name ?? $this->parameter;
    }

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function toArray()
    {
        return [
            '@type' => 'QueryParameter',
            'parameter' => $this->parameter,
            'name' => $this->name,
        ];
    }
}
