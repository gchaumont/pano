<?php

namespace Pano\Middleware;

use Closure;
use Pano\Pano;

 class PanoMiddleware
 {
     public function handle($request, Closure $next, $application)
     {
         resolve(Pano::class)
             ->setRootApp($application)
         ;

         // resolve(Pano::class)
         //     ->setCurrentApp(resolve(Pano::class)->resolveApp(request()->route()->getName()))

         return $next($request);
     }
 }
