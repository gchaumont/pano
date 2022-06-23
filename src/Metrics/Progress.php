<?php

namespace Pano\Metrics;

use Pano\Metrics\Results\ProgressResult;

  /**
   *  Metric.
   */
  abstract class Progress extends Metric
  {
      const TYPE = 'progress';

      public function count($request, $model, callable $query): ProgressResult
      {
          // code...
      }

      public function sum($request, $model, callable $query, string $field): ProgressResult
      {
          // code...
      }
  }
