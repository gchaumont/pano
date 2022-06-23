<?php

namespace Pano\Metrics;

use Elastico\Models\Builder\Builder;
use InvalidArgumentException;
use Pano\Metrics\Results\MetricResult;
use Pano\Metrics\Results\ValueResult;
use Pano\Pano;

  /**
   *  Metric.
   */
  abstract class Value extends Metric
  {
      const TYPE = 'value';

      protected string|int $defaultRange;

      protected string|null $prefix = null;

      protected string|null $suffix = null;

      public function count(
          $request,
          Builder|string $model,
          null|string $dateField = null,
          callable $callback = null
      ): ValueResult {
          return $this->aggregate($request, $model, 'count', null, $dateField, $callback);
      }

      public function average(
          $request,
          Builder|string $model,
          string $field,
          null|string $dateField = null,
          callable $callback = null
      ): ValueResult {
          return $this->aggregate($request, $model, 'avg', $field, $dateField, $callback);
      }

      public function sum(
          $request,
          Builder|string $model,
          string $field,
          null|string $dateField = null,
          callable $callback = null
      ): ValueResult {
          return $this->aggregate($request, $model, 'sum', $field, $dateField, $callback);
      }

      public function max(
          $request,
          Builder|string $model,
          string $field,
          null|string $dateField = null,
          callable $callback = null
      ): ValueResult {
          return $this->aggregate($request, $model, 'max', $field, $dateField, $callback);
      }

      public function min(
          $request,
          Builder|string $model,
          string $field,
          null|string $dateField = null,
          callable $callback = null
      ): ValueResult {
          return $this->aggregate($request, $model, 'min', $field, $dateField, $callback);
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
              // 365 => '365 Days',
              // 'TODAY' => 'Today',
              // 'YESTERDAY' => 'Yesterday',
              // 'MTD' => 'Month To Date',
              // 'QTD' => 'Quarter To Date',
              // 'YTD' => 'Year To Date',
          ];
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

      protected function currentRange(string|int|null $range, $timezone): array
      {
          if (is_null($range)) {
              return [];
          }

          if (ctype_digit(strval($range))) {
              return ["now-{$range}d/d", 'now/m'];
          }

          return match ($range) {
              'TODAY' => ['now/d', 'now/m'],
              'YESTERDAY' => ['now-2d/d', 'now-1d/d'],
              'MTD' => ['now/M', 'now/m'],
              'QTD' => ['now-3M/M', 'now/m'],
              'YTD' => [now()->startOfYear(), now()],
            default => throw new InvalidArgumentException("Unknown Date Range [{$range}]")
          };
      }

      protected function previousRange(string|int|null $range, $timezone): array
      {
          if (is_null($range)) {
              return [];
          }

          if (ctype_digit(strval($range))) {
              $doubleRange = $range * 2;

              return ["now-{$doubleRange}d/d", "now-{$range}d/d"];
          }

          return match ($range) {
              'TODAY' => ['now-2d/d', 'now-1d/m'],
              'YESTERDAY' => ['now-2d/d', 'now-1d/d'],
              'MTD' => ['now-1M/M', 'now-1M/M'],
              'QTD' => ['now-6M/M', 'now-3M/M'],
              'YTD' => [now()->subYear()->startOfYear(), now()->startOfYear()],
            default => throw new InvalidArgumentException("Unknown Date Range [{$range}]")
          };
      }

      protected function result(mixed $result): MetricResult
      {
          return (new ValueResult())->result($result);
      }

      protected function aggregate(
          $request,
          Builder|string $model,
          string $function,
          null|string $column = null,
          null|string $dateColumn = null,
          null|callable $callback = null,
      ) {
          $query = $model instanceof Builder ? $model : $model::query();

          if ('count' !== $function) {
              $column = $column ?? $query->getModel()->getQualifiedKeyName();
          }

          $timezone = resolve(Pano::class)->resolveUserTimezone($request) ?? $request->timezone;

          $range = $request->input('range') ?? array_key_first($this->ranges());

          $result = $this->result(
              round((clone $query)->whereBetween(
                  $dateColumn ?? $query->getModel()->getCreatedAtColumn(),
                  $this->currentRange($range, $timezone)
              )->when(!empty($callback), fn ($q) => $callback($q))
                  ->{$function}($column), $this->precision)
          );
          if ($prevRange = $this->previousRange($range, $timezone)) {
              $previousValue = round((clone $query)->whereBetween(
                  $dateColumn ?? $query->getModel()->getCreatedAtColumn(),
                  $prevRange
              )->when(!empty($callback), fn ($q) => $callback($q))
                  ->{$function}($column), $this->precision);

              $result->previous($previousValue);
          }

          return $result;
      }
  }
