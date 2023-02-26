<?php

namespace Pano\Fields;

use Pano\Fields\Concerns\HasOptions;
use Pano\Query\Directives\Directive;
use Pano\Query\Directives\FieldDirective;

class Badge extends Field
{
    use HasOptions;

    protected bool $with_icons = false;

    protected array $field_map = [
        'info' => 'info',
        'success' => 'success',
        'danger' => 'danger',
        'warning' => 'warning',
    ];

    protected array $type_map = [
        'info' => 'badge-blue',
        'success' => ['badge-green', 'bold'],
    ];

    protected array $icon_map = [
    ];

    public function getDirective(): null|Directive
    {
        if (!empty($this->field)) {
            return new FieldDirective($this->field);
        }

        return null;
    }

    public function map(array $fields): static
    {
        $this->field_map = $fields;

        return $this;
    }

    public function types(array $types): static
    {
        $this->type_map = array_merge($types, $this->type_map);
    }

    public function withIcons(bool $icons = true): static
    {
        $this->with_icons = $icons;

        return $this;
    }

    public function addTypes(array $types): static
    {
    }

    public function data($request): array
    {
        return [
            'options' => $this->getOptions($request),
        ];
    }

    public function config(): array
    {
        return [
            ...parent::config(),
            'map' => $this->field_map,
            'withIcons' => $this->with_icons,
        ];
    }
}
