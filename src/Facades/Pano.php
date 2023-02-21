<?php

namespace Pano\Facades;

use Illuminate\Support\Facades\Facade;

class Pano extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'pano';
    }
}
