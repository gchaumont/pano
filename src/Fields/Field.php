<?php

namespace Pano\Fields;

use Elastico\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pano\Components\Component;
use Pano\Fields\Concerns\HasFilters;
use Pano\Fields\Concerns\HasFormat;
use Pano\Fields\Concerns\HasOptions;
use Pano\Fields\Concerns\HasVisibility;
use Pano\Resource\Resource;

/**
 * Represents an object attribute and provides
 * information on how to access, filter,
 * display and sort the property.
 */
abstract class Field extends Component
{
    use HasVisibility;
    use HasFormat;
    use HasFilters;
    use HasOptions;

    protected mixed $value;

    protected string $field;

    protected Nested $nestedUnder;

    protected mixed $default;

    protected string $placeholder;

    protected $transform;

    protected bool $sortable = false;

    protected \Closure|bool $searchable = false;

    protected bool $required;
    protected bool $stacked = false;
    protected string $help;
    protected bool $readonly = false;

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
                throw new \InvalidArgumentException('Invalid Field Parameter.');
            }
        } else {
            $this->field = Str::snake($name);
        }
    }

    public function field(): null|array|string
    {
        return $this->field ?? null;
    }

    public function getResource(): Resource
    {
        $context = $this->getContext();

        while (!is_a($context, Resource::class)) {
            $context = $context->getContext();
        }

        return $context;
    }

    public function searchable(\Closure|bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function applySearch(Builder $builder, mixed $value): Builder
    {
        if (is_callable($this->searchable)) {
            return ($this->searchable)($builder, $value);
        }

        return $builder->where($this->field(), 'like', '%'.trim($value).'%');
    }

    public static function make(...$args): static
    {
        return new static(...$args);
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

    public function isSearchable(): bool
    {
        return true === $this->searchable || $this->searchable instanceof \Closure;
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

    /**
     * Prepare value to be sent to Front.
     *
     * @param mixed $object
     */
    public function serialiseValue(object $object): mixed
    {
        $value = $this->resolveValue($object);

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

    public function data($request): array
    {
        return [
            'filterable' => $this->isFilterable($request),
            'options' => fn ($request) => $this->getOptions($request),
        ];
    }

    public function config(): array
    {
        return array_filter([
            ...parent::config(),
            'key' => $this->getKey(),
            'type' => $this->getType(),
            'name' => $this->getName(),
            'help' => $this->help ?? null,
            'field' => $this->field(),
            'sortable' => $this->isSortable(),
            'align' => $this->textAlign ?? null,
            'format' => $this->format ?? null,
        ]);
    }

    public function namespace(string $namespace): static
    {
        $this->namespace = $namespace;

        return $this;
    }

    protected function resolveValue(object $object): mixed
    {
        // Apply user transform
        if (!empty($this->resolveUsing)) {
            return call_user_func($this->resolveUsing, $object);
        }
        // Get the value from the model
        return $object->getAttribute($this->field());
    }
}
