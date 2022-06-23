<?php

namespace Pano\Query\Directives;

use Elastico\Aggregations\Bucket\Terms;
use Elastico\Query\Builder;
use Elastico\Query\FullText\MultiMatchQuery;

class FuzzyKeyValuePatternDirective extends PatternDirective
{
    public function __construct(
        protected string $key,
        protected array $fields,
        protected int|string|null $fuzziness = 'auto'
    ) {
    }

    public static function forField(string $key, string $field): static
    {
        return new static($key, [$field]);
    }

    public static function forFields(string $key, string ...$fields): static
    {
        return new static($key, $fields);
    }

    public function apply(Builder $builder, string $pattern, array $values, int $patternOffsetStart, int $patternOffsetEnd): void
    {
        $builder->must((new MultiMatchQuery())->query($values['value'])->fields($this->fields)->fuzziness($this->fuzziness));

        if (false === $this->useSuggestions) {
            return;
        }

        foreach ($this->fields as $field) {
            $builder->addAggregation((new Terms("_{$field}_suggestions"))->field("{$field}"));
        }
    }

    public function pattern(): string
    {
        return "/{$this->key}:(?<value>.*?)(?:$|\\s)/i";
    }

    public function transformToSuggestions(array $results): array
    {
        if (false === $this->useSuggestions) {
            return [];
        }

        $validAggregations = array_map(
            fn (string $field) => "_{$field}_suggestions",
            $this->fields
        );

        return collect($results['aggregations'] ?? [])
            ->filter(fn (array $aggregation, string $name) => in_array($name, $validAggregations))
            ->flatMap(fn (array $aggregation) => array_map(
                fn (array $bucket) => Suggestion::fromBucket($bucket),
                $aggregation['buckets']
            ))
            ->toArray()
        ;
    }

    public function setFuzziness(string|int|null $fuzziness): self
    {
        $this->fuzziness = $fuzziness;

        return $this;
    }
}
