<?php

namespace Pano\Menu;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        // $this->name($this->item->getName());
        // $this->key($this->getKey());
        $this->key('mGroup-'.Str::random(5));
    }

    public function getName(): string
    {
        return $this->item->getName();
    }
}
