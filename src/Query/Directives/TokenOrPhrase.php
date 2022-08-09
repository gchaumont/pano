<?php

namespace Pano\Query\Directives;

use Elastico\Query\Query;
use Elastico\Query\Term\Prefix;
use Elastico\Query\Term\Term;

class TokenOrPhrase extends PatternDirective
{
    public function __construct(public string $field, public null|string $path)
    {
        // code...
    }

    public function getType(): string
    {
        return 'field';
    }

    public function getAlias(): array
    {
        // code...
    }

    public function getDescription()
    {
        return 'Filter results that contain "'.$this->field.'"';
    }

    // public function query($value): Query
    // {
    //     if (str_ends_with($value, '*')) {
    //         return Prefix::make()->field($this->field)->value(substr($value, 0, -1));
    //     }

    //     return Term::make()->field($this->field)->value($value);
    // }

    public function pattern(): string
    {
        return '/(?:"(.*?)")|(?:[^\(^\)^\{^\}\s]+)/i';
    }

    public function isComplete(): bool
    {
        return true;
    }

    public function suggest($builder): array
    {
        return $this->complete($builder);
    }

    public function complete($builder): array
    {
        $field = implode('.', array_filter([$this->path, $this->field]));

        return $builder->enumerate($field, string : $this->match ?? '', size: 10)
            ->take(20)
            ->filter()
            // ->filter(fn ($term) => str_starts_with(strtolower($term), strtolower($query)))
            ->map(fn ($e) => [
                'type' => 'term',
                'text' => str_contains($e, ' ') ? '"'.$e.'"' : $e,
                'start' => $this->_debug['index'] ?? null,
            ])
            ->values()
            ->all()
        ;
    }
}
