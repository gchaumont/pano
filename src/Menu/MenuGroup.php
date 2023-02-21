<?php

namespace Pano\Menu;

use Illuminate\Support\Collection;

class MenuGroup extends MenuItems
{
    public string $component = 'MenuGroup';

    public Collection $items;

    public readonly MenuItem $item;

    protected bool $collapsable = true;

    public function __construct(
        string|MenuItem $name,
        iterable $items,
    ) {
        $this->items = collect($items);
        $this->item = is_string($name) ? MenuItem::make($name) : $name;
    }

    public function getName(): string
    {
        return $this->item->getName();
    }
}
