<?php

namespace Pano\Fields\Relation;

use Pano\Fields\Field;
use Pano\Pano;
use Pano\Query\Handlers\ResourceQueryHandler;
use Pano\Resource\Resource;

 abstract class Relation extends Field
 {
     const TYPE = 'relation';

     public string $resource;

     public null|string $appPath = null;

     /**
      * Method invoked by system when eager loading
      * a relation on a collection of models.
      */
     abstract public function load(array $models): array;

     /**
      * Method invoked by the system when querying
      * a relation to a specific model.
      */
     abstract public function query(Resource $resource, string $key): ResourceQueryHandler;

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

         return resolve(Pano::class)->resolveApp($this->namespace)->resource($this->resource);

         return resolve(Pano::class)->currentApp()->resource($this->resource);
     }

     public function getModel(): string
     {
         return $this->getResource()->model;
     }
 }
