<?php

namespace Pano\Fields;

use Pano\Query\Directives\Directive;
use Pano\Query\Directives\NestedFieldDirective;

/**
 * Shows Nested data.
 */
class Nested extends Field
{
    protected array $fields;

    protected int $max;

    protected \Closure|bool $visibleOnIndex = false;

    public function getDirective(): null|Directive
    {
        if (!empty($this->field)) {
            return new NestedFieldDirective(
                $this->field,
                directives: collect($this->fields)->map(fn ($field) => $field->getDirective())->filter()->values()->all(),
            );
        }

        return null;
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function max(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Prepare value to be sent to Front.
     */
    public function serialiseValue(object $object): mixed
    {
        $list = [];
        foreach ($this->resolveValue($object) ?? [] as $nested) {
            $entity = [];
            foreach ($this->fields as $field) {
                $entity[] = $field->serialiseValue($nested);
            }
            $list[] = $entity;
        }

        return $list;
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            'fields' => collect($this->fields)->map(fn ($field) => $field->config())->all(),
            'max' => $this->max ?? null,
        ];
    }

    public function namespace(string $namespace): static
    {
        $this->fields = collect($this->fields)
            ->map(fn ($field) => $field->namespace($namespace))
            ->all()
        ;

        $this->namespace = $namespace;

        return $this;
    }
}
