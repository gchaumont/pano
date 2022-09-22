<?php

namespace Pano\Fields;

use Closure;
use Elastico\Models\Builder\Builder;
use Elastico\Models\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Pano\Fields\Concerns\HasFormat;
use Pano\Fields\Concerns\HasVisibility;
use Pano\Query\Directives\Directive;

abstract class Field
{
    use HasVisibility;
    use HasFormat;

    protected mixed $value;

    protected string $field;

    protected Nested $nestedUnder;

    protected mixed $default;

    protected string $placeholder;

    protected $transform;

    protected Closure|bool $filterable = true;

    protected bool $sortable = false;

    protected bool $required;
    protected bool $stacked = false;
    protected string $help;
    protected bool $readonly = false;

    protected null|Directive $directive = null;

    public function __construct(
        public string $name,
        null|string|callable $field = null,
    ) {
        if (!empty($field)) {
            if (is_string($field)) {
                $this->field = $field ?? Str::snake($name);
            } elseif (is_callable($field)) {
                // $this->value = $field();
                $this->resolveUsing($field);
            // $this->sortable(false);
            } else {
                throw new InvalidArgumentException('Invalid Field Parameter.');
            }
        } else {
            $this->field = Str::snake($name);
        }
    }

    public function getDirective(): ?Directive
    {
        return $this->directive;
    }

    public function field(): null|array|string
    {
        return $this->field ?? null;
    }

    public static function make(string $name, string|callable $field = null): static
    {
        return new static(name: $name, field: $field);
    }

    public function default(mixed $value): static
    {
        $this->default = is_callable($value) ? $value() : $value;

        return $this;
    }

    public function placeholder(string $value): static
    {
        $this->placeholder = $value;

        return $this;
    }

    public function transform(callable $callblack): static
    {
        $this->transform = $callblack;

        return $this;
    }

    public function applyTransform(array $object): mixed
    {
        if ($this->transform) {
            $value = Arr::get($object, $this->field);
            $call = $this->transform;

            $value = $call($value);

            Arr::set($object, $this->field, $value);
        }

        return $object;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function fillUsing(callable $callable): static
    {
        $this->fillUsing = $callable;
        $callable = function ($request, $model, $attribute, $requestAttribute) {
            $model->{$attribute} = Str::title($request->input($attribute));
        };

        return $this;
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
        $this->required = $required;

        return $this;
    }

    public function isRequired($request): bool
    {
        return is_callable($this->required) ? call_user_func($this->required, $request) : $required;
    }

    public function nullable(bool|callable $nullable = true): static
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function isNullable($request): bool
    {
        return is_callable($this->nullable) ? call_user_func($nullable, $request) : $nullable;
    }

    public function nullValues(array|callable $nullValues): static
    {
        $this->nullValues = is_callable($nullValues) ? $nullValues() : $nullValues;

        return $this;
    }

    public function stacked(bool $stacked = true): static
    {
        $this->stacked = $stacked;

        return $this;
    }

    public function filterable(bool|callable $filterable = true): static
    {
        $this->filterable = $filterable;

        return $this;
    }

    public function applyFilter($request, $query, $value, $attribute): Builder
    {
        return is_callable($this->filterable) ? call_user_func($this->filterable, func_get_args()) : $query;
    }

    public function isFilterable($request): bool
    {
        return !empty($this->filterable) && $this->filterable;
    }

    /**
     * Prepare value to be sent to Front.
     *
     * @param mixed $object
     */
    public function serialiseValue(object $object): mixed
    {
        // Apply user transform
        if (!empty($this->resolveUsing)) {
            $value = call_user_func($this->resolveUsing, $object);
        } else {
            // Get the value from the model
            $value = $object->getAttribute($this->field());
        }

        if (is_null($value) && isset($this->default)) {
            $value = is_callable($this->default) ? call_user_func($this->default, $object) : $this->default;
        }

        // Apply user transform only for display (not input)
        if (!empty($this->displayUsing)) {
            return call_user_func($this->displayUsing, $value, $object);
        }

        // Apply field type specific transformation
        return $this->formatValue($value);
    }

    public function formatValue(mixed $value): mixed
    {
        return $value;
    }

    public function dependsOn(array $fields, callable $closure): static
    {
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
        $this->displayUsing = $callable;

        return $this;
    }

    public function readonly(bool|callable $readonly = true): static
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function isReadonly($request): bool
    {
        return is_callable($this->readonly) ? call_user_func($this->readonly, $request) : $readonly;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getKey(): string
    {
        $key = $this->field() ?? $this->getName();

        return strtolower(Str::slug($key));
    }

    public function getType(): string
    {
        return defined(static::class.'::TYPE') ? static::TYPE.'-field' : strtolower(class_basename(static::class)).'-field';
    }

    public function jsonConfig($request): array
    {
        return array_filter([
            'key' => $this->getKey(),
            'type' => $this->getType(),
            'name' => $this->getName(),
            'help' => $this->help ?? null,
            'field' => $this->field(),
            'sortable' => $this->isSortable(),
            'filterable' => $this->isFilterable($request),
            'align' => $this->textAlign ?? null,
            'format' => $this->format ?? null,
        ]);
    }
}
