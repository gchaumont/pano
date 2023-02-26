<?php

namespace Pano\Fields\Groups;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pano\Components\Component;
use Pano\Concerns\HasFields;
use Pano\Fields\Concerns\HasVisibility;

/**
 * Group of fields displayed vertically.
 */
class Stack extends Component
{
    use HasVisibility;
    use HasFields;

    public bool $showInEdit = false;

    public function __construct(
        public string $name,
        array $fields,
    ) {
        $this->fields = collect($fields);
    }

    public function getKey(): string
    {
        return $this->key ?? Str::slug($this->name);
    }

    public function isSearchable(): bool
    {
        return false;
    }

    public function config(): array
    {
        return [
            'key' => $this->getKey(),
            'type' => 'stack-field',
            'name' => $this->getName(),
            'fields' => collect($this->fields)
                ->keyBy(fn ($f) => $f->getKey())
                ->map(fn ($f) => $f->config())
                ->all(),
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
        return collect($this->fields)
            ->keyBy(fn ($f) => $f->getKey())
            ->map(fn ($field) => $field->serialiseValue($resource))
            ->all()
        ;
    }

    public function getContexts(): Collection
    {
        return $this->getFields();
    }

    // public function namespace(string $namespace): static
    // {
    //     $this->fields = collect($this->fields)
    //         ->map(fn ($field) => $field->namespace($namespace))
    //         ->all()
    //     ;
    //     $this->namespace = $namespace;

    //     return $this;
    // }
}
