<?php

namespace Pano\Fields\Relation;

use Closure;

abstract class RelatesToMany extends Relation
{
    const TYPE = 'relates-to-many';

    public bool|Closure $visibleOnIndex = false;
}
