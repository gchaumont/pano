<?php

namespace Pano\Middleware;

use Closure;
use Pano\Pano;

 class PanoMiddleware
 {
     public function handle($request, Closure $next, $application)
     {
         resolve(Pano::class)
             ->setCurrentApp($application)
         ;

         return $next($request);
     }
 }
