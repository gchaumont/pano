<?php

namespace Pano\Forms;

use Pano\Components\Component;

class SelectMenu extends Component
{
    public \Closure|array $options;

    public string $component = 'pano-select-menu';

    public function collapsable(bool $collapsable = true): static
    {
        $this->collapsable = $collapsable;

        return $this;
    }

    public function options(array|\Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getProps(): array
    {
        return [
            'options' => $this->getOptions(),
        ];
    }

    public function getOptions(): array
    {
        return is_callable($this->options) ? ($this->options)() : $this->options;
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
