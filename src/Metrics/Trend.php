<?php

namespace Pano\Metrics;

use Elastico\Aggregations\Aggregation;
use Elastico\Aggregations\Bucket\DateHistogram;
use Elastico\Aggregations\Metric\Avg;
use Elastico\Aggregations\Metric\Max;
use Elastico\Aggregations\Metric\Min;
use Elastico\Aggregations\Metric\Sum;
use Elastico\Models\Builder\Builder;
use Pano\Metrics\Results\TrendResult;
use Pano\Pano;

 /**
  * Trend Metric.
  */
 abstract class Trend extends Metric
 {
     const TYPE = 'trend';

     public string|null $defaultRange;

     protected string|null $prefix = null;

     protected string|null $suffix = null;

     public function countByMonths($request, string|Builder $model, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'count', null, 'M', $dateField);
     }

     public function countByWeeks($request, string|Builder $model, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'count', null, 'w', $dateField);
     }

     public function countByDays($request, string|Builder $model, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'count', null, 'd', $dateField);
     }

     public function countByHours($request, string|Builder $model, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'count', null, 'h', $dateField);
     }

     public function countByMinutes($request, string|Builder $model, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'count', null, 'm', $dateField);
     }

     public function avgByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'avg', $field, 'M', $dateField);
     }

     public function avgByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'avg', $field, 'w', $dateField);
     }

     public function avgByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'avg', $field, 'd', $dateField);
     }

     public function avgByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'avg', $field, 'h', $dateField);
     }

     public function avgByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'avg', $field, 'm', $dateField);
     }

     public function sumByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'sum', $field, 'M', $dateField);
     }

     public function sumByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'sum', $field, 'w', $dateField);
     }

     public function sumByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'sum', $field, 'd', $dateField);
     }

     public function sumByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'sum', $field, 'h', $dateField);
     }

     public function sumByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'sum', $field, 'm', $dateField);
     }

     public function maxByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'max', $field, 'M', $dateField);
     }

     public function maxByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'max', $field, 'w', $dateField);
     }

     public function maxByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'max', $field, 'd', $dateField);
     }

     public function maxByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'max', $field, 'h', $dateField);
     }

     public function maxByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'max', $field, 'm', $dateField);
     }

     public function minByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'min', $field, 'M', $dateField);
     }

     public function minByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'min', $field, 'w', $dateField);
     }

     public function minByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'min', $field, 'd', $dateField);
     }

     public function minByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'min', $field, 'h', $dateField);
     }

     public function minByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
     {
         return $this->aggregateBy($request, $model, 'min', $field, 'm', $dateField);
     }

     /**
      * Get the ranges available for the metric.
      *
      * @return array
      */
     public function ranges()
     {
         return [
             // 30 => '30 Days',
             // 60 => '60 Days',
             // 90 => '90 Days',
         ];
     }

     public function defaultRange(string $range): static
     {
         $this->defaultRange = $range;

         return $this;
     }

     public function getDefaultRange(): string|null
     {
         return $this->defaultRange ?? array_key_first($this->ranges());
     }

     public function jsonConfig(): array
     {
         return [
             ...parent::jsonConfig(),
             'defaultRange' => $this->getDefaultRange(),
             'ranges' => collect($this->ranges())->map(fn ($range, $key) => ['key' => $key, 'name' => $range])->values(),
             'prefix' => $this->prefix,
             'suffix' => $this->suffix,
         ];
     }

     protected function getAggregation(string $function, string $field): Aggregation
     {
         return match ($function) {
             'sum' => (new Sum('agg'))->field($field),
            'max' => (new Max('agg'))->field($field),
            'min' => (new Min('agg'))->field($field),
            'avg' => (new Avg('agg'))->field($field),
         };
     }

     protected function currentRange(string|int $range, string $timeUnit): array
     {
         return ["now-{$range}{$timeUnit}/{$timeUnit}", 'now/m'];
     }

     protected function aggregateBy(
         $request,
         Builder|string $model,
         string $function,
         null|string $field = null,
         string $timeUnit,
         null|string $dateField = null
     ): TrendResult {
         $query = $model instanceof Builder ? $model : $model::query();

         if ('count' !== $function) {
             $field = $field ?? $query->getModel()->getQualifiedKeyName();
         }

         $timezone = resolve(Pano::class)->resolveUserTimezone($request) ?? $request->timezone;
         $trend = $query->whereBetween(
             $dateField,
             $this->currentRange($request->range, $timeUnit, $timezone)
         )->addAggregation(
             (new DateHistogram('trend'))->field($dateField)->calendarInterval('1'.$timeUnit)->format('yyyy-MM-dd')
                 ->when('count' !== $function, fn ($hist) => $hist->addAggregation($this->getAggregation($function, $field)))
         )
             ->take(0)
             ->get()
             ->aggregation('trend')
             ->buckets()
             ->pluck('count' === $function ? 'doc_count' : 'agg.value')
             ->all()
         ;

         return (new TrendResult())->trend($trend);
     }
 }
