<?php

namespace Pano\Concerns;

use Illuminate\Support\Str;

trait Nameable
{
    protected string $name;

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name ?? Str::headline(class_basename(static::class));
    }
}
