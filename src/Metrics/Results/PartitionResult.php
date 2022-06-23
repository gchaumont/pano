<?php

namespace Pano\Metrics\Results;

  /**
   *  Metric.
   */
  class PartitionResult extends MetricResult
  {
      protected array $partition;

      protected array $label;

      protected array $color;

      public function partition(array $partition): static
      {
          $this->partition = $partition;

          return $this;
      }

      public function label(callable $callback): static
      {
          foreach ($this->partition as &$part) {
              $part['name'] = $callback($part['name']);
          }

          return $this;
      }

      public function colors(callable $callable): string
      {
          $this->color = $callable;

          return $this;
      }

      public function transform(callable $callback): static
      {
          foreach ($this->partition as &$part) {
              $part['value'] = $callback($part['value']);
          }

          return $this;
      }

      public function toJson(): array
      {
          return [
              'partition' => $this->partition,
              'prefix' => $this->prefix,
              'suffix' => $this->suffix,
          ];
      }
  }
