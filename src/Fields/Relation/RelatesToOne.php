<?php

namespace Pano\Fields\Relation;

use Pano\Fields\Concerns\HasOptions;

abstract class RelatesToOne extends Relation
{
    use HasOptions;

    const TYPE = 'relates-to-one';

    public bool|\Closure $visibleOnIndex = true;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->options(
            fn ($request) => $this->getRelatedResource()
                ->newQuery()
                ->take(10)
                ->get()
                ->keyBy(fn ($record) => $record->getKey())
                ->map(fn ($record) => $this->getRelatedResource()->getTitle($record))
                ->all()
        );
    }

    public function data($request): array
    {
        return [
            'options' => $this->getOptions($request),
        ];
    }
}
