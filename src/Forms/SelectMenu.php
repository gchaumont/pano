<?php

namespace Pano\Forms;

use Illuminate\Support\Collection;
use Pano\Components\Component;

class SelectMenu extends Component
{
    public Collection $items;

    public string $component = 'pano-select-menu';

    public function collapsable(bool $collapsable = true): static
    {
        $this->collapsable = $collapsable;

        return $this;
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            '@type' => 'FormInput',
            'name' => $this->getName(),

            // 'options' => $this->getOptions(),
        ];
    }
}
