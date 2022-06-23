<?php

namespace Pano\Metrics;

use DateTime;
use Pano\Concerns\Linkable;
use Pano\Metrics\Results\MetricResult;

 /**
  *  Metric.
  */
 abstract class Metric
 {
     use Linkable;

     public int $precision = 2;

     abstract public function calculate($request): MetricResult;

     public function asJson($request): array
     {
         return $this->calculate($request)->toJson();
     }

     public function make(): static
     {
         return new static(...func_get_args());
     }

     public function cacheFor(): int|null|DateTime
     {
         return -1;
     }

     public function precision(int $precision): static
     {
         $this->precision = $precision;

         return $this;
     }

     public function jsonConfig(): array
     {
         return [
             'type' => $this->getType(),
             'name' => $this->getName(),
             'key' => $this->getUriKey(),
             'path' => $this->getPath(),
         ];
     }

     protected function getType(): string
     {
         return static::TYPE;
     }
 }
