<?php

namespace Pano\Menu;

use Illuminate\Support\Str;
use Pano\Application\Application;
use Pano\Components\Component;
use Pano\Concerns\Nameable;
use Pano\Menu\Concerns\HasStatus;

class MenuItem extends Component
{
    use Nameable;
    use HasStatus;

    public string $component = 'pano-menu-item';

    protected null|string|\Closure $icon = null;

    protected string|\Closure $path;

    protected bool $canSee;

    protected bool $inactive = false;

    public function __construct(
        string $name,
        string $link = null,
    ) {
        $this->name($name);
        // $this->key(Str::slug($this->name).'-'.Str::random(5));
        $this->key('mItem-'.Str::random(5));
        // if ($link) {
        //     $static->path($link);
        // }
    }

    public function inactive(bool $inactive = true): static
    {
        $this->inactive = $inactive;

        return $this;
    }

    // public function path(string $path): static
    // {
    //     $this->path = $path;

    //     return $this;
    // }
    public function path(string|\Closure $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getName(): string
    {
        return is_string($this->name) ? $this->name : ($this->name)($this->getApplication());
    }

    public function getPath(): string
    {
        return is_string($this->path) ? $this->path : ($this->path)($this->getApplication());
    }

    public function getIcon(): ?string
    {
        if (empty($this->icon)) {
            return null;
        }
        $icon = is_string($this->icon) ? $this->icon : ($this->icon)($this->getApplication());

        $icon = Str::finish($icon, '-icon');

        return ucfirst(Str::camel($icon));
    }

    public function route(string $name): static
    {
        $this->route = $name;

        return $this;
    }

    public function canSee(bool|callable $canSee): bool
    {
        $this->canSee = is_bool($canSee) ? $canSee : $canSee();

        return $this;
    }

    public function icon(string|null $icon): static
    {
        $this->icon = $icon;

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
    public static function application(string $application, array|callable $items = null): static
    {
        $static = new static('');
        $static->path = fn (Application $app) => $app->getApp($application)
            ->url()
        ;
        $static->name = fn (Application $app) => $app->getApp($application)
            ->getName()
        ;
        $static->icon = fn (Application $app) => $app->getApp($application)
            ->getIcon()
        ;

        return $static;
    }

    public static function dashboard(string $dashboard): static
    {
        $static = new static('');
        $static->path = fn (Application $app) => $app->getDashboard($dashboard)
            ->url()
        ;
        $static->name = fn (Application $app) => $app->getDashboard($dashboard)
            ->getName()
        ;
        $static->icon = fn (Application $app) => $app->getDashboard($dashboard)
            ->getIcon()
        ;

        return $static;
    }

    public static function resource(string $resource): static
    {
        $static = new static('');
        $static->path = fn (Application $app) => $app->getResource($resource)
            ->url()
        ;

        $static->name = fn (Application $app) => $app->getResource($resource)
            ->getName()
        ;
        $static->icon = fn (Application $app) => $app->getResource($resource)
            ->getIcon()
        ;

        return $static;
    }

    public function getUriKey(): string
    {
        return '';
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            'type' => 'item',
            'format' => 'default',
            'icon' => $this->getIcon(),
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'inactive' => $this->inactive,
            'status' => $this->getStatus(),
        ];
    }
}
