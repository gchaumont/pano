<?php

namespace Pano\Metrics\Results;

  /**
   *  Metric.
   */
  class ProgressResult extends MetricResult
  {
      protected array $progress;

      public function progress(array $progress): static
      {
          $this->progress = $progress;

          return $this;
      }
  }
