<?php

namespace Pano\Resource;

use Pano\Query\Handlers\EloquentQueryHandler;

abstract class EloquentResource extends Resource
{
    public string $queryHandler = EloquentQueryHandler::class;
}
