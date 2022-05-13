<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;
use Pano\Application;
use Pano\Pano;

 class ResourceController extends Controller
 {
     public Application $app;

     public function app(string $resource)
     {
         return view('Pano::pano');
     }

     public function index(string $resource)
     {
         $app = resolve(Pano::class)->currentApp();
         $resource = $app->resource($resource);

         $fields = collect($resource->fields())->map(fn ($field) => $field->field())->filter()->all();

         return $resource->model::query()->select($fields)->take(50)->get()->hits();
     }

     public function store(string $resource, Request $request)
     {
     }

     public function update(string $resource, string $item)
     {
     }

     public function destroy(string $resource, string $item)
     {
     }
 }
