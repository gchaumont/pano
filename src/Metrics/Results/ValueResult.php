<?php

namespace Pano\Metrics\Results;

  /**
   *  Metric.
   */
  class ValueResult extends MetricResult
  {
      public function result(int|float|array $result): static
      {
          $this->result = $result;

          return $this;
      }

      public function transform(callable $callable): static
      {
          $this->result($callable($this->result));
          if (isset($this->previous)) {
              $this->previous($callable($this->previous));
          }

          return $this;
      }

      public function toJson(): array
      {
          return [
              'result' => $this->result,
              'previous' => $this->previous ?? null,
              'prefix' => $this->prefix,
              'suffix' => $this->suffix,
          ];
      }
  }
