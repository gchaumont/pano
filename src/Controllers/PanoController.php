<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;
use Pano\Pano;

 class PanoController extends Controller
 {
     /**
      * Get the list of applications for
      * route registering in the front-end.
      */
     public function apps()
     {
     }

     /**
      * Get one application's configuration.
      */
     public function config()
     {
         return resolve(Pano::class)->currentApp()->jsonConfig();
     }

     /**
      * Global search
      * Search multiple apps at once?
      */
     public function search()
     {
         // code...
     }
 }
