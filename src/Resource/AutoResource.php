<?php

namespace Pano\Resource;

use Pano\Fields\ID;
use Pano\Fields\Text;

class AutoResource extends Resource
{
    public function __construct(public string $model)
    {
    }

    public function fields(): array
    {
        return [
            ID::make('ID', '_id'),
            Text::make('Name'),
        ];
    }
}
