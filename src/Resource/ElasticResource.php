<?php

namespace Pano\Resource;

use Pano\Query\Handlers\ElasticoQueryHandler;

abstract class ElasticResource extends Resource
{
    public string $queryHandler = ElasticoQueryHandler::class;
}
