<?php

namespace Pano\Concerns;

use Illuminate\Support\Str;

trait Identifiable
{
    public string $id;

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id ??= static::class;

        return $this->id ??= (Str::slug(class_basename(static::class)).'-'.Str::random(5));
    }
}
