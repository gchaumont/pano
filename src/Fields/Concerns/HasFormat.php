<?php

namespace Pano\Fields\Concerns;

trait HasFormat
{
    protected null|string $format = null;
    protected null|string $textAlign = null;

    public function textAlign(string $align): static
    {
        $this->textAlign = $align;

        return $this;
    }

    public function asHeading(): static
    {
        $this->format = 'heading';

        return $this;
    }

    public function asSmall(): static
    {
        $this->format = 'small';

        return $this;
    }

    public function asSubtitle(): static
    {
        $this->format = 'subtitle';

        return $this;
    }

    public function asBase(): static
    {
        $this->format = 'base';

        return $this;
    }
}
