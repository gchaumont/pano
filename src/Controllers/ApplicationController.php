<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;
use Pano\Application;

 class ApplicationController extends Controller
 {
     public Application $app;

     public function home()
     {
         return view('Pano::pano');
     }
 }
