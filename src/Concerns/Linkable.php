<?php

namespace Pano\Concerns;

use Illuminate\Support\Str;
use Pano\Application\Application;
use Pano\Pano;

 trait Linkable
 {
     use Nameable;

     protected string $key;

     protected null|string $namespace;

     protected string $uriKey;

     protected string $path_prefix;

     public function key(string $key): static
     {
         $this->key = $key;

         return $this;
     }

     public function uriKey(string $key): static
     {
         $this->uriKey = $key;

         return $this;
     }

     public function getKey(): string
     {
         return $this->key ?? static::class;
     }

     public function getRouteKey(): string
     {
         return $this->key ?? $this->uriKey ?? Str::slug($this->getName());
     }

     public function getRoute(): string
     {
         return implode('.', [$this->getNamespace(), $this->getRouteKey()]);
     }

     public function getUriKey(): string
     {
         return $this->uriKey ?? $this->key ?? Str::slug($this->getName());
     }

     public function application(Application $app): static
     {
         return $this->namespace($app->getRoute());
     }

     public function url(bool $relative = true): string
     {
         return route($this->getRoute(), [], !$relative);
     }

     /**
      * The Application Namespace.
      */
     public function namespace(null|string $namespace): static
     {
         $this->namespace = $namespace;

         return $this;
     }

     public function pathPrefix(string $prefix): static
     {
         $this->pathPrefix = $prefix;

         return $this;
     }

     /**
      * Path prefix inside application
      * Represents folders.
      */
     public function getPathPrefix(): string|null
     {
         return $this->pathPrefix ?? null;
     }

     /**
      * Path inside the application.
      */
     public function getPath(): string
     {
         return implode('/', array_filter([$this->getPathPrefix(), $this->getUriKey()]));
     }

     public function getContainingApp(): Application
     {
         return resolve(Pano::class)->resolveApp($this->getNamespace());
     }

     public function getNamespace(): string|null
     {
         return $this->namespace;
     }
 }
