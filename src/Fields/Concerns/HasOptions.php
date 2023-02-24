<?php

namespace Pano\Fields\Concerns;

trait HasOptions
{
    protected \Closure|array $options = [];

    public function options(array|callable $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions($request): array
    {
        return collect($this->options instanceof \Closure ? $this->options($request) : $this->options)
            ->map(fn ($label, $value) => ['label' => $label, 'value' => $value])
            ->all()
        ;
    }
}
