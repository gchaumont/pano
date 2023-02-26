<?php

namespace Pano;

use Pano\Application\Application;
use Pano\Concerns\HasContexts;
use Pano\Concerns\Identifiable;
use Pano\Facades\Pano;

abstract class Context
{
    use Identifiable;
    use HasContexts;

    const CONTEXT_SEPARATOR = '.';

    protected readonly null|string $context;

    protected readonly null|string $application;

    protected \Closure $onRegistered;

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    final public function register(Context $context = null): static
    {
        if ($context) {
            $this->setContext($context);
        }

        $this->getRoot()->remember($this->getId(), $this);

        $this->getContexts()
            ->each(fn (Context $c) => $c->register($this))
        ;

        return $this;
    }

    public function registered(): void
    {
        isset($this->onRegistered) && ($this->onRegistered)($this);
        unset($this->onRegistered);

        $this->getContexts()
            ->each(fn ($context) => $context->registered())
        ;
    }

    public function onRegistered(\Closure $callback): static
    {
        $this->onRegistered = $callback;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->id ??= collect($this->getNamespace())
            ->push($this->getKey())
            ->filter(fn ($l) => !empty($l))
            ->implode($this->getContextSeparator())
        ;
    }

    public function hasParent(): bool
    {
        return (bool) $this->getContext();
    }

    public function getNamespace(): ?string
    {
        return $this->_namespace ??= $this->getContext()?->getLocation();
    }

    public function setContext(Context $context): static
    {
        $this->context = $context->getLocation();

        if ($context instanceof Application) {
            $this->setApplication($context);
        } elseif ($context->getApplication()) {
            $this->setApplication($context->getApplication());
        }

        return $this;
    }

    public function getRoot(): Context
    {
        return $this->hasParent() ? $this->getContext()->getRoot() : $this;
    }

    public function getApplication(): ?Application
    {
        return isset($this->application) ? Pano::context($this->application) : null;
    }

    public function getRootApplication(): ?Application
    {
        $app = $this->getApplication();

        return $app?->getApplication() ?? $app;
    }

    public function setApplication(Application $application): static
    {
        $this->application = $application->getLocation();

        return $this;
    }

    public function getContext(): ?Context
    {
        return isset($this->context) ? Pano::context($this->context) : null;
    }

    public function getContextSeparator(): string
    {
        if (!$this->hasParent()) {
            return '';
        }

        return $this->getContext()::CONTEXT_SEPARATOR;
    }
}
