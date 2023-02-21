<?php

namespace Pano\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Pano\Application\Application;
use Pano\Concerns\HasPages;
use Pano\Concerns\Nameable;
use Pano\Context;
use Pano\Controllers\PageController;
use Pano\Pages\Page;
use Pano\Routes\RouteRecord;

/**
 * Part of the User Interface.
 */
abstract class Component extends Context
{
    use HasPages;
    use Nameable;

    public string $component;

    public array $props = [];

    public Collection $children;

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function definition()
    {
        return [
            'component' => $this->getComponent(),
            'props' => $this->getProps(),
        ];
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
            ->keyBy(fn ($child) => $child->getId())
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

    public function registerRoutes(): void
    {
        Route::group(array_filter([
            // 'middleware' => $this->getMiddleware()->push('pano:'.$this->getLocation())->all(),
            // 'as' => $this->getContextSeparator().$this->getId(),
            // 'domain' => $this->getDomain(),
            // 'prefix' => $this->getPath(),
        ]), function () {
            if ($this instanceof Page) {
                // dump($this->getId());
                Route::get('', PageController::class)->name('');
            }

            Route::controller(PageController::class)
                ->group(
                    fn () => $this->getContexts()
                        // ->tap(fn ($a) => dump($a->keys()))
                        ->each(fn ($item) => $item instanceof Page
                               && Route::get($item->getPath())->name($this->getContextSeparator().$item->getRoute()))
                        ->each(fn (Component $item) => $item->registerRoutes())
                )
            ;
            if ($this instanceof Application) {
                Route::fallback(fn () => redirect($pages->first()->getPath()))->name($this->getContextSeparator().'404');
            }
        });
    }
}
