<?php

namespace Pano\Middleware;

use Illuminate\Support\Facades\View;
use Pano\Facades\Pano;

class SetupApplication
{
    public function handle($request, \Closure $next, $application)
    {
        $app = Pano::context($application);

        Pano::setCurrentApp($app);

        // Pano::bootCurrentApp();

        // dd(Pano::manager());

        if (!$request->wantsJson()) {
            View::share('panoConfig', \Pano\Facades\Pano::getContexts()->map(fn ($context) => $context->definition())->values());
        }

        return $next($request);
    }
}
