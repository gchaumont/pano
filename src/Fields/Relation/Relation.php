<?php

namespace Pano\Fields\Relation;

use Pano\Facades\Pano;
use Pano\Fields\Field;
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
            return Pano::context($this->appPath)->getResource($this->resource);
        }

        return Pano::context($this->namespace)->getResource($this->resource);

        return Pano::getCurrentApp()->getResource($this->resource);
    }

        public function jsonConfig($request): array
        {
            return [
                // 'resource' => $this->getResource()->config(),
                ...parent::jsonConfig($request),
            ];
        }

    public function getModel(): string
    {
        return $this->getResource()->model;
    }
}
