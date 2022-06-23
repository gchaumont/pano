<?php

namespace Pano\Metrics\Results;

  /**
   *  Metric.
   */
  class TrendResult extends MetricResult
  {
      protected array $trend;

      public function trend(array $trend): static
      {
          $this->trend = $trend;

          return $this;
      }

      public function transform(callable $transform): static
      {
          foreach ($this->trend as &$trend) {
              $trend = $transform($trend);
          }

          return $this;
      }

      public function toJson(): array
      {
          return [
              'trend' => $this->trend,
              'suffix' => $this->suffix,
              'prefix' => $this->prefix,
          ];
      }
  }
