<?php

namespace Pano\Menu;

use Illuminate\Support\Str;
use Pano\Application\Application;
use Pano\Concerns\Linkable;

class MenuItem
{
    use Linkable;

    protected null|string $icon = null;
    protected string $path;

    protected bool $canSee;

    public function __construct(
        string $name,
    ) {
        $this->name($name);
    }

    // public function path(string $path): static
    // {
    //     $this->path = $path;

    //     return $this;
    // }

    public function canSee(bool|callable $canSee): bool
    {
        $this->canSee = is_bool($canSee) ? $canSee : $canSee();

        return $this;
    }

    public static function make(string $name, string $link = null)
    {
        $static = new static(name: $name);
        // if ($link) {
        //     $static->path($link);
        // }

        return $static;
    }

    public function icon(string|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public static function link(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public static function externalLink(string $name, string $path): static
    {
        // code...
    }

    /**
     * Display a link to an application
     * Items can be passed as options for the main parameter.
     */
    public static function application(string $app, array|callable $items = null): static
    {
        $static = new static(name: $app);
        $static->application = $app;

        return $static;
    }

    public static function dashboard(string $dashboard): static
    {
        $static = new static(name: $dashboard);
        $static->dashboard = $dashboard;

        return $static;
    }

    public static function resource(string $resource): static
    {
        $static = new static('');
        $static->resource = $resource;

        return $static;
    }

    public function namespace(string $namespace): static
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getUriKey(): string
    {
        return '';
    }

    public function pathPrefix(string $prefix): static
    {
        $this->pathPrefix = $prefix;

        if (!empty($this->application)) {
            $this->getContainingApp()->application($this->application)->pathPrefix($this->getPath());
        } elseif (!empty($this->resource)) {
            $this->getContainingApp()->resource($this->resource)->pathPrefix($this->getPath());
        } elseif (!empty($this->dashboard)) {
        }

        return $this;
    }

    public function jsonConfig(): array
    {
        if (!empty($this->application)) {
            $this->name($this->getContainingApp()->application($this->application)->getName());
            $path = $this->getContainingApp()->application($this->application)->url();
        } elseif (!empty($this->resource)) {
            $resource = $this->getContainingApp()->resource($this->resource);
            $this->name($resource->getName());
            $path = $resource->url();
        } elseif (!empty($this->dashboard)) {
            $dashboard = $this->getContainingApp()->dashboard($this->dashboard);
            $this->name($dashboard->getName());
            $path = $dashboard->url();
        }

        return [
            'type' => 'item',
            'format' => 'default',
            'icon' => empty($this->icon) ? null : ucfirst(Str::camel($this->icon.'-icon')),
            'name' => $this->getName(),
            'path' => $path,
        ];
    }
}
