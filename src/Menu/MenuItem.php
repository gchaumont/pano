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

    protected null|string $icon = null;

    protected string $path;

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
    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    // public function getName(): string
    // {
    //     return is_string($this->name) ? $this->name : ($this->name)($this->getApplication());
    // }

    public function getPath(): string
    {
        return $this->path;
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

        $static->onRegistered(function (MenuItem $item) use ($application): void {
            $app = $item->getApplication()->getApp($application);
            $item->path = $app->url();
            $item->name = $app->getName();
            $item->icon ??= $app->getIcon();
        });

        return $static;
    }

    public static function dashboard(string $dashboard): static
    {
        $static = new static('');
        $static->onRegistered(function (MenuItem $item) use ($dashboard): void {
            $dash = $item->getApplication()->getDashboard($dashboard);
            $item->path = $dash->url();
            $item->name = $dash->getName();
            $item->icon ??= $dash->getIcon();
        });

        return $static;
    }

    public static function resource(string $resource): static
    {
        $static = new static('');
        $static->name('');

        $static->onRegistered(function (MenuItem $item) use ($resource): void {
            $resource = $item->getApplication()->getResource($resource);
            $item->path = $resource->url();
            $item->name = $item->name ?: $resource->getName();
            $item->icon ??= $resource->getIcon();
        });

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
