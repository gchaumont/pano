<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Pano\Pano;

 class DashboardController extends Controller
 {
     public function show()
     {
         return view('Pano::pano');
     }

     public function load($dashboard)
     {
         return $dashboard;
     }

     public function metric($metric)
     {
         $metric = resolve(Pano::class)
             ->resolveFromRoute(request()->route()->getName())
             ->getMetric($metric)
         ;

         return Cache::remember('pano:metrics:'.$metric->getRouteKey(), $metric->cacheFor(), fn () => $metric->asJson(request()));
     }
 }
