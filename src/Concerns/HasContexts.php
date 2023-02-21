<?php

namespace Pano\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Application\Application;
use Pano\Context;
use Pano\Pages\Page;

trait HasContexts
{
    protected Collection $contexts;

    public function context(string $context): Application|Page|Context
    {
        $path = $context;
        if (Str::contains($path, '.')) {
            return $this->context(Str::before($path, '.'))->context(Str::after($context, '.'));
        }

        return $this->getContexts()->get($path)
            // ?? dd($this->getContexts()->keys(), $path)
            ?? throw new \Exception("The context '{$context}' is not registered in ".($this->getLocation() ?: 'the root namespace'));
    }

    public function getContexts(): Collection
    {
        return $this->contexts ??= collect();
    }
}
