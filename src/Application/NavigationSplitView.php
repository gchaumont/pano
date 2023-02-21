<?php

namespace Pano\Application;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Components\Component;
use Pano\Routes\RouteRecord;

class NavigationSplitView extends Component
{
    public string $route = '';

    public function __construct(
        public $sidebar,
        public $homepage = null,
        public Collection $children,
    ) {
        // code...
    }

    public function getId(): string
    {
        return $this->id ??= (Str::slug(class_basename(static::class)).'-'.Str::random(5));
    }

    public function getRouteRecords(): Collection
    {
        return collect()
            ->push(
                new RouteRecord(
                    component: $this->getComponent(),
                    path: $this->url(),
                    name: $this->getLocation(),
                    props: collect($this->getProps()),
                    redirect: $this->homepage,
                    children: $this->getChildren()
                        ->flatMap(fn (Component $component) => $component->getRouteRecords()->values())
                )
            )
        ;
    }
}
