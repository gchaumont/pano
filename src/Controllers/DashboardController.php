<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;

 class DashboardController extends Controller
 {
     public function show($dashboard)
     {
         return view('Pano::pano');
     }
 }
