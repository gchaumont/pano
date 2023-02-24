<?php

namespace Pano\Endpoints;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Pano\Concerns\Routable;
use Pano\Context;

/**
 * Part of the User Interface
 * reachable by Url.
 */
class Endpoint extends Context implements Arrayable
{
    use Routable;
    public string $method;

    public mixed $handler;

    public function __construct()
    {
    }

    // call static method with http verb that sets method on class instance
    // and returns it
    public static function __callStatic($name, $arguments)
    {
        $static = new static();
        if (in_array($name, ['get', 'head', 'post', 'put', 'patch', 'delete'])) {
            $static->method($name);
        }
        $static->name(Str::slug($arguments[0]));
        $static->key('endpoints-'.Str::slug($name.$arguments[0]));
        $static->route('endpoints-'.Str::slug($name.$arguments[0]));
        $static->path($arguments[0]);

        return $static;
    }

    public function toArray()
    {
        return collect(get_object_vars($this))->filter(fn ($v) => !is_null($v))->toArray();
    }

    public function method(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function handler(mixed $handler): static
    {
        $this->handler = $handler;

        return $this;
    }

    public function registerRoute(): void
    {
        Route::{$this->method}($this->path, $this->handler)->name($this->getContextSeparator().$this->getRoute());
    }

    public function config(): array
    {
        return [
            'method' => $this->method,
            'url' => $this->url(),
            'name' => $this->name,
        ];
    }
}
