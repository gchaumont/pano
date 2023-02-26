<?php

namespace Pano\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Application\Application;
use Pano\Context;
use Pano\Pages\Page;

trait HasContexts
{
    public static array $remember = [];

    public static array $aliased = [];

    protected Collection $contexts;

    public function context(string $context): Application|Page|Context
    {
        if (isset(static::$aliased[$context])) {
            return static::$aliased[$context];
        }
        if (isset(static::$remember[$context])) {
            return static::$remember[$context];
        }
        // dump(static::class.' - '.$context);
        // dump(array_keys(static::$remember));
        $path = $context;
        if (Str::contains($path, '.')) {
            // try {
            return $this->context(Str::before($path, '.'))->context(Str::after($context, '.'));
            // } catch (\Exception $e) {
            //     // dd($this);

            //     throw $e;
            // }
        }

        return $this->getContexts()->get($path)
            // ?? response([static::class, $this->getContexts()->keys()->all(), $path])->send()
            // ?? dd(static::class, $this->getContexts()->keys()->all(), $path, $this, static::$remember, static::$aliased)
            ?? throw new \Exception("The context '{$context}' is not registered in ".($this->getLocation() ?: 'the root namespace'));
    }

    public function getContexts(): Collection
    {
        return $this->contexts ??= collect();
    }

    public function remember(string $key, Context $context): static
    {
        if (isset(static::$remember[$key])) {
            // dump(static::$remember);

            throw new \Exception("The context '{$key}' has already been registered in ".($this->getLocation() ?: 'the root namespace'));
        }

        static::$remember[$key] = $context;

        if ($alias = $context->getAlias()) {
            $this->rememberAlias($alias, $context);
        }

        return $this;
    }

    public function rememberAlias(string $key, Context $context): static
    {
        if (isset(static::$aliased[$key])) {
            throw new \Exception("The alias '{$key}' has already been registered the root namespace");
        }

        static::$aliased[$key] = $context;

        return $this;
    }
}
