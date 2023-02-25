<?php

namespace Pano\Menu;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Components\Component;

class MenuItems extends Component
{
    public Collection $items;

    public string $component = 'pano-menu-menu-items';

    public function __construct(
        iterable $items,
    ) {
        $this->items = collect($items);
        $this->key(Str::slug(class_basename(static::class)).'-'.Str::random(5));
    }

    public function collapsable(bool $collapsable = true): static
    {
        $this->collapsable = $collapsable;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->items;
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            'type' => 'group',
            'name' => $this->getName(),
            'collapsable' => $this->collapsable,
            'items' => $this->items->map(fn ($i) => $i->config())->values(),
        ];
    }
}
