<?php

namespace Pano\Query\Directives;

use Elastico\Query\Query;
use Elastico\Query\Term\Range;

class CompareDirective extends Directive
{
    public function __construct(
        protected string $field,
        protected array $values,
    ) {
        $this->directives = $values;
    }

    public function handle(Directive $node, null|Directive $parent, mixed $root): void
    {
        $query = (new Range())->field($this->field);

        $this->getCurrentQuery()->filter(match ($this->match) {
            '>' => $query->gt($node->match),
            '>=' => $query->gte($node->match),
            '<=' => $query->lte($node->match),
            '<' => $query->lt($node->match),
        });
        // dump(spl_object_id($this->getCurrentQuery()));
        // dump(
        //     spl_object_id($parent->parent).'  -   '.$node->_debug['input'],
        //     $node->parent,
        //     $this,
        //     $parent->parent,
        //     $this->getCurrentQuery(),
        // );
    }

    public function complete($builder): array
    {
        return collect([
            '>' => 'is greater than to some value',
            '>=' => 'is greater than or equal to some value',
            '<' => 'is less than to some value',
            '<=' => 'is less than or equal to some value',
        ])->map(fn ($t, $key) => [
            'type' => 'compare',
            'text' => $key,
            'description' => $t,
        ])
            ->all()
        ;
    }

    public function directives(): array
    {
        return $this->directives;
    }

    public function getType(): string
    {
        return 'field';
    }

    public function getText()
    {
        return ':';
    }

    public function getAlias(): array
    {
        // code...
    }

    public function getDescription()
    {
        return 'Filter results that contain "'.$this->field.'"';
    }

    public function query($value): ?Query
    {
        return null;
    }

    public function pattern(): string
    {
        return '/(?:>=|>|<=|<)/';
    }
}
