<?php

namespace Pano\Menu;

class MenuGroup extends MenuItems
{
    protected bool $collapsable = true;

    public function __construct(
        public readonly MenuItem $item,
        public array $items,
    ) {
    }

    public function getName(): string
    {
        return $this->item->getName();
    }

    public function collapsable(bool $collapsable = true): static
    {
        $this->collapsable = $collapsable;

        return $this;
    }

    public static function make(string|MenuItem $name, array $items): static
    {
        if (is_string($name)) {
            $name = MenuItem::make($name);
        }

        return new static(item : $name, items: $items);
    }

    public function jsonConfig(): array
    {
        return [
            'type' => 'group',
            'name' => $this->getName(),
            'collapsable' => $this->collapsable,
            'items' => array_values(array_map(fn ($item) => $item->jsonConfig(), $this->items)),
        ];
    }
}
