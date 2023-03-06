<?php

namespace Pano\Fields\Concerns;

trait HasOptions
{
    protected \Closure|array $options;

    public function options(array|callable $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions($request): array
    {
        if (!$this->isFilterable($request)) {
            return [];
        }

        if (!isset($this->options)) {
            return $this->getResource()
                ->fieldOptions($this->field)
                ->all()
            ;
        }

        return collect($this->options instanceof \Closure ? ($this->options)($request) : $this->options)
            ->map(fn ($label, $value) => ['label' => $label, 'value' => $value])
            ->values()
            ->all()
        ;
    }
}
