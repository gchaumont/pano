<?php

namespace Pano\Fields;

/**
 * Display select form.
 */
class Select extends Field
{
    protected array $options = [];

    protected bool $searchable = false;

    // Display the selected key by default
    protected bool $display_using_labels = false;

    public function options(array|callable $options): static
    {
        $this->options = is_callable($options) ? $options() : $options;

        return $this;
    }

    public function displayUsingLabels(): static
    {
        $this->display_using_labels = true;

        return $this;
    }

    // public function searchable(): static
    // {
     //     $this->searchable = true;

     //     return $this;
    // }
}
