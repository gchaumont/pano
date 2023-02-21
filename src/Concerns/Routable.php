<?php

namespace Pano\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait Routable
{
    public ?string $domain = null;

    public ?string $path = null;

    public array $parameters = [];

    public array $middleware = [];

    public ?string $route = null;

    public function getDomain(): null|string
    {
        return $this->domain;
    }

    public function getPath(): string
    {
        return $this->path ?? $this->defaultPath();
    }

    public function getRoute(): string
    {
        return $this->route ?? $this->getId();

        return $this->route ?? Str::slug($this->getPath());
    }

    public function getMiddleware(): Collection
    {
        return collect($this->middleware);
    }

    public function domain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function middleware(array|string $middleware): static
    {
        $this->middleware = is_string($middleware) ? [$middleware] : $middleware;

        return $this;
    }

    public function route(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function parameters(...$args): static
    {
        $this->parameters = $args;

        return $this;
    }

    public function url(): string
    {
        // return route($this->getRouteNamespace(), $this->parameters, false);

        return route($this->getLocation(), $this->parameters, false);
    }

    public function getRouteNamespace(): string
    {
        return collect($this->getNamespace())
            ->push($this->getRoute())
            ->filter(fn ($route) => !empty($route))
            ->implode($this->getContextSeparator())
        ;
    }

    protected function defaultPath(): string
    {
        return Str::slug($this->getName());
    }
}
