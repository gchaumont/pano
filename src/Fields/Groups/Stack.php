<?php

namespace Pano\Fields\Groups;

use Illuminate\Support\Str;
use Pano\Fields\Concerns\HasVisibility;

/**
 * Group of fields displayed vertically.
 */
class Stack
{
    use HasVisibility;

    public bool $showInEdit = false;

    public function __construct(
        public string $name,
        public array $fields,
    ) {
    }

    public static function make(string $name, array $items): static
    {
        return new static($name, $items);
    }

    public function getKey(): string
    {
        return $this->key ?? Str::slug($this->name);
    }

    public function isSearchable(): bool
    {
        return false;
    }

    public function jsonConfig($request): array
    {
        return [
            'key' => $this->getKey(),
            'type' => 'stack-field',
            'name' => $this->getName(),
            'fields' => collect($this->fields)->keyBy(fn ($f) => $f->getKey())->map(fn ($f) => $f->jsonConfig($request))->all(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSortable(): bool
    {
        return false;
    }

    public function field(): null|array|string
    {
        return collect($this->fields)->map(fn ($field) => $field->field())->all();
    }

    public function serialiseValue(mixed $resource): mixed
    {
        return collect($this->fields)->keyBy(fn ($f) => $f->getKey())->map(fn ($field) => $field->serialiseValue($resource))->all();
    }

    public function fields(): array
    {
        return $this->fields;
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
