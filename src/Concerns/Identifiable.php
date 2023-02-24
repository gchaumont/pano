<?php

namespace Pano\Concerns;

use Illuminate\Support\Str;

trait Identifiable
{
    protected readonly string $key;

    protected readonly string $alias;

    private readonly string $id;

    public function key(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): string
    {
        // if (isset($this->id)) {
        //     return $this->id;
        // }
        if (isset($this->key)) {
            return $this->key;
        }

        return Str::slug($this->getName() ?? class_basename(static::class));
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getAlias(): null|string
    {
        return $this->alias ?? null;
    }

    public function getId(): string
    {
        return $this->getLocation();
    }
}
