<?php

namespace Pano\Fields\Relation;

use Pano\Fields\Field;
use Pano\Pano;

 abstract class Relation extends Field
 {
     const TYPE = 'relation';

     public string $resource;

     public null|string $appPath = null;

     public function resource(string $resource, string $appPath = null): static
     {
         $this->resource = $resource;
         $this->appPath = $appPath;

         return $this;
     }

     public function getResource()
     {
         if ($this->appPath) {
             return resolve(Pano::class)->resolveApp($this->appPath)->resource($this->resource);
         }

         return resolve(Pano::class)->currentApp()->resource($this->resource);
     }

     public function getModel(): string
     {
         return $this->getResource()->model;
     }
 }
