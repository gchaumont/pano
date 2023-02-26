<?php

namespace Pano\Fields\Relation;

use Pano\Facades\Pano;
use Pano\Fields\Field;
use Pano\Query\Handlers\ResourceQueryHandler;
use Pano\Resource\Resource;

abstract class Relation extends Field
{
    const TYPE = 'relation';

    public string $related;

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

    public function resource(string $resource): static
    {
        $this->related = $resource;

        return $this;
    }

    public function getRelatedResource(): Resource
    {
        // return $this->getApplication()->getResource($this->resource);
        // if ($this->appPath) {
        //     return Pano::context($this->appPath)->getResource($this->resource);
        // }
        return $this->getRoot()->context($this->related);
        // return Pano::context($this->related)->getResource($this->resource);
    }

    public function config(): array
    {
        return [
            // 'resource' => $this->getResource()->config(),
            ...parent::config(),
        ];
    }

    public function getModel(): string
    {
        return $this->getResource()->model;
    }
}
