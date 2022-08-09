<?php

namespace Pano\Query\Directives;

use Elastico\Query\FullText\MatchPhraseQuery;
use Elastico\Query\Query;
use Elastico\Query\Term\Prefix;
use Elastico\Query\Term\Term;

class ContainsDirective extends BooleanDirective
{
    public function __construct(
        protected string $field,
        protected array $values,
        protected bool $multiple = true,
        protected null|string $path = null
    ) {
        parent::__construct(directives: $values);
    }

    public function handle(Directive $node, null|Directive $parent, mixed $root): void
    {
        parent::handle($node, $parent, $root);

        if ($node instanceof TokenOrPhrase) {
            if (str_starts_with($node->match, '"')) {
                // $this->getCurrentQuery()->filter(
                //     (new Term())->field($this->field)->value(substr($node->match, 1, -1))
                // );
                $this->getCurrentQuery()->filter(
                    (new MatchPhraseQuery())->field($this->field)->message(substr($node->match, 1, -1))
                );
            } elseif (str_ends_with($node->match, '*')) {
                $this->getCurrentQuery()->filter(
                    (new Prefix())->field($this->field)->value(substr($node->match, 0, -1))
                );
            } else {
                $this->getCurrentQuery()->filter(
                    (new Term())->field($this->field)->value($node->match)
                );
            }
        }
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

    public function suggest($builder): array
    {
        return collect($this->directives)->first()->suggest($builder);
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
        return '/:/';
    }
}
