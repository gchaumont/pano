<?php

namespace Pano\Fields\Relation;

use Closure;

abstract class RelatesToOne extends Relation
{
    const TYPE = 'relates-to-one';

    public bool|Closure $visibleOnIndex = true;
}
