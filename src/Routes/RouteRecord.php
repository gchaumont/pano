<?php

namespace Pano\Routes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Part of the User Interface
 * reachable by Url.
 */
class RouteRecord implements Arrayable
{
    public function __construct(
        protected ?string $component = null,
        protected ?Collection $components = null,
        protected ?string $path = null,
        protected ?string $name = null,
        protected ?string $redirect = null,
        protected ?Collection $props = null,
        protected ?Collection $children = null,
        protected ?Collection $meta = null,
    ) {
    }

    public static function component(
        string $component,
        string $path = null,
        string $name = null,
        iterable $props = [],
        iterable $children = [],
        iterable $meta = [],
    ): static {
        return new static(
            ...collect(get_defined_vars())
                ->map(fn ($value) => is_iterable($value) ? collect($value) : $value)
                ->all()
        );
    }

    public static function redirect(
        string $path,
        string $redirect,
    ): static {
        return new static(path: $path, redirect: $redirect);
    }

    public function toArray()
    {
        return collect(get_object_vars($this))->filter(fn ($v) => !is_null($v))->toArray();
    }
}
