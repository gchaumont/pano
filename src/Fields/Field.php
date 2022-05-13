<?php

namespace Pano\Fields;

use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class Field
{
    protected mixed $value;

    protected string $field;

    protected mixed $default;

    protected string $placeholder;

    protected string $alignment;

    protected bool $filterable;

    protected bool $required;
    protected bool $stacked = false;
    protected string $help;
    protected bool $readonly = false;

    protected bool $visibleOnIndex;
    protected bool $visibleOnDetail;
    protected bool $visibleOnCreating;
    protected bool $visibleOnUpdating;
    protected bool $visibleOnPreview;

    final public function __construct(
        public string $name,
        null|string|callable $field = null,
    ) {
        if (!empty($field)) {
            if (is_string($field)) {
                $this->field = $field ?? Str::snake($name);
            } elseif (is_callable($field)) {
                $this->value = $field();
            } else {
                throw new InvalidArgumentException('Invalid Field Parameter.');
            }
        } else {
            $this->field = Str::snake($name);
        }
    }

    public function field(): ?string
    {
        return $this->field ?? null;
    }

    public static function make(string $name, string|callable $field = null): static
    {
        return new static(name: $name, field: $field);
    }

    public function default(callable $value): static
    {
        if (is_callable($value)) {
            $value = $value();
        }
        $this->default = $value;

        return $this;
    }

    public function placeholder(string $value): static
    {
        $this->placeholder = $value;

        return $this;
    }

    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function fillUsing(callable $callable): static
    {
        $this->fillUsing = $callable;
        $callable = function ($request, $model, $attribute, $requestAttribute) {
            $model->{$attribute} = Str::title($request->input($attribute));
        };

        return $this;
    }

    public function showOnIndex(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function showOnDetail(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function showOnCreating(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function showOnUpdating(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function showOnPreview(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function hideFromIndex(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function hideFromDetail(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function hideWhenCreating(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function hideWhenUpdating(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function onlyOnIndex(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function onlyOnDetail(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function onlyOnForms(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function exceptOnForms(callable $condition = null): static
    {
        if (!empty($condition)) {
            $this->visibility = $condition();
        } else {
            $this->visibility = true;
        }
    }

    public function creationRules(string|Rule ...$rules): static
    {
        $this->creationRules = func_get_args();

        return $this;
    }

    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function updateRules(string|Rule ...$rules): static
    {
        $this->creationRules = func_get_args();

        return $this;
    }

    public function afterValidation(callable $closure): static
    {
        // code...
    }

    public function afterCreationValidation(callable $closure): static
    {
        // code...
    }

    public function afterUpdateValidation(callable $closure): static
    {
        // code...
    }

    public function required(bool|callable $required = true): static
    {
        $this->required = is_callable($required) ? $required() : $required;

        return $this;
    }

    public function nullable(bool|callable $nullable = true): static
    {
        $this->nullable = is_callable($nullable) ? $nullable() : $nullable;

        return $this;
    }

    public function nullValues(array|callable $nullValues): static
    {
        $this->nullValues = is_callable($nullValues) ? $nullValues() : $nullValues;

        return $this;
    }

    public function stacked(bool|callable $stacked = true): static
    {
        $this->stacked = is_callable($stacked) ? $stacked() : $stacked;

        return $this;
    }

    public function filterable(bool|callable $filterable = true): static
    {
        $this->filterable = is_callable($filterable) ? $filterable() : $filterable;

        return $this;
    }

    public function dependsOn(array $fields, callable $closure): static
    {
    }

    public function textAlign(string|callable $alignment): static
    {
        $this->alignment = is_callable($alignment) ? $alignment() : $alignment;

        return $this;
    }

    public function help(array $help): static
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Modify field when coming in from DB.
     */
    public function resolveUsing(callable $callable): static
    {
        $this->resolveUsing = $callable;

        return $this;
    }

    /**
     * Modify field before sending to FrontEnd.
     */
    public function displayUsing(callable $callable): static
    {
        $this->resolveUsing = $callable;

        return $this;
    }

    public function readonly(bool|callable $readonly = true): static
    {
        $this->readonly = is_callable($readonly) ? $readonly() : $readonly;

        return $this;
    }

    public function canSee(callable $canSee): static
    {
        $this->canSee = $canSee;

        return $this;
    }

    public function canSeeWhen(string $action, object $authorizable): static
    {
        // Checks the policy
    }

    public function jsonConfig(): array
    {
        return [
            'name' => $this->name,
            'field' => $this->field(),
            'sortable' => $this->sortable ?? false,
        ];
    }
}
