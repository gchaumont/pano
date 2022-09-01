<?php

namespace Pano\Metrics;

use Elastico\Aggregations\Aggregation;
use Elastico\Aggregations\Bucket\Filter;
use Elastico\Aggregations\Bucket\Terms;
use Elastico\Aggregations\Metric\Avg;
use Elastico\Aggregations\Metric\Max;
use Elastico\Aggregations\Metric\Min;
use Elastico\Aggregations\Metric\Sum;
use Elastico\Query\Builder;
use Elastico\Query\MatchAll;
use Pano\Metrics\Results\PartitionResult;
use Pano\Pano;

  /**
   *  Metric.
   */
  abstract class Partition extends Metric
  {
      const TYPE = 'partition';

      protected string|null $prefix = null;

      protected string|null $suffix = null;

      public function count($request, $model, $by, callable $callback = null): PartitionResult
      {
          return $this->aggregateBy($request, $model, 'count', null, $by, $callback);
      }

      public function sum($request, $model, $field, $by, callable $callback = null): PartitionResult
      {
          return $this->aggregateBy($request, $model, 'sum', $field, $by, $callback);
      }

      public function average($request, $model, $field, $by, callable $callback = null): PartitionResult
      {
          return $this->aggregateBy($request, $model, 'avg', $field, $by, $callback);
      }

      public function max($request, $model, $field, $by, callable $callback = null): PartitionResult
      {
          return $this->aggregateBy($request, $model, 'max', $field, $by, $callback);
      }

      public function min($request, $model, $field, $by, callable $callback = null): PartitionResult
      {
          return $this->aggregateBy($request, $model, 'min', $field, $by, $callback);
      }

      public function jsonConfig(): array
      {
          return [
              ...parent::jsonConfig(),
              'prefix' => $this->prefix,
              'suffix' => $this->suffix,
          ];
      }

      protected function getAggregation(string $function, string $field = null): Aggregation
      {
          return match ($function) {
              'count' => (new Filter('agg'))->filter(new MatchAll()),
              'sum' => (new Sum('agg'))->field($field),
              'max' => (new Max('agg'))->field($field),
              'min' => (new Min('agg'))->field($field),
              'avg' => (new Avg('agg'))->field($field),
          };
      }

      protected function aggregateBy(
          $request,
          Builder|string $model,
          string $function,
          null|string $field = null,
          null|string $by = null,
          null|callable $callback = null,
      ) {
          $query = $model instanceof Builder ? $model : $model::query();

          if ('count' !== $function) {
              $field = $field ?? $query->getModel()->getQualifiedKeyName();
          }

          $timezone = resolve(Pano::class)->resolveUserTimezone($request) ?? $request->timezone;

          $results = $query->take(0)
              ->addAggregation(
                  (new Terms('terms'))->field($by)->size(5)->addAggregation($this->getAggregation($function, $field))
              )
              ->when(!empty($callback), fn ($q) => $callback($q))
              ->get()
              ->aggregation('terms')
              ->buckets()
              ->map(fn ($bucket) => [
                  'name' => $bucket['key'],
                  'value' => 'count' == $function ? $bucket['doc_count'] : $bucket['agg']['value'],
              ])
              ->all()
          ;

          return (new PartitionResult())->partition($results)->field($by);
      }
  }
