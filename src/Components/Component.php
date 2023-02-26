<?php

namespace Pano\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Pano\Concerns\HasPages;
use Pano\Concerns\Nameable;
use Pano\Context;
use Pano\Fields\Concerns\HasData;
use Pano\Pages\Page;
use Pano\Routes\RouteRecord;

/**
 * Part of the User Interface.
 */
abstract class Component extends Context
{
    use HasPages;
    use Nameable;
    use HasData;

    public string $component;

    public array $props = [];

    public Collection $children;

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function config()
    {
        return [
            '@type' => 'Component',
            'uiPath' => $this->uiPath(),
            'component' => $this->getComponent(),
            'props' => $this->getProps(),
        ];
    }

    public function uiPath(): string
    {
        $path = collect();
        $context = $this;
        while (!empty($context) && !is_subclass_of($context, Page::class)) {
            $path->push($context->getKey());
            $context = $context->getContext();
        }

        return $path->reverse()->implode('.');
    }

    public function getComponent(): string
    {
        return $this->component ?? class_basename(static::class);
    }

    public function children(iterable $children): static
    {
        $this->children = collect($children);

        return $this;
    }

    public function getChildren(): Collection
    {
        // return $this->pages ??= collect();

        return collect($this->children ?? []);
    }

    public function url(): string
    {
        return '';
    }

    /**
     * Build the Vue Route configuration.
     */
    public function getRouteRecords(): Collection
    {
        return collect()
            ->push(
                RouteRecord::component(
                    component: $this->getComponent(),
                    path: $this->url(),
                    name: $this->getLocation(),
                    props: $this->getProps(),
                    children: $this->getChildren()
                        ->flatMap(fn (Component $component) => $component->getRouteRecords()->values())
                )
            )
        ;
    }

    public function getContexts(): Collection
    {
        return $this->getChildren()
            ->concat($this->getPages())
            ->keyBy(fn ($child) => $child->getKey())
        ;
    }

    public function getProps(): array
    {
        return $this->props;
    }

    public function props(array $props): static
    {
        $this->props = $props;

        return $this;
    }

    /**
     * Register potential Routes of Child Pages.
     */
    public function getRoutes(): \Closure
    {
        return function () {
            $this->getContexts()
                ->each(fn (Component $component) => Route::group([], $component->getRoutes()))
            ;
        };
    }
}
