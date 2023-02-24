<?php

namespace Pano\Metrics;

use Elastico\Aggregations\Aggregation;
use Elastico\Aggregations\Bucket\DateHistogram;
use Elastico\Aggregations\Metric\Avg;
use Elastico\Aggregations\Metric\Max;
use Elastico\Aggregations\Metric\Min;
use Elastico\Aggregations\Metric\Sum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Pano\Facades\Pano;
use Pano\Metrics\Results\TrendResult;

/**
 * Trend Metric.
 */
abstract class Trend extends Metric
{
    const TYPE = 'trend';

    const UNIT_MONTH = 'M';

    const UNIT_WEEK = 'w';

    const UNIT_DAY = 'd';

    const UNIT_HOUR = 'h';

    const UNIT_MINUTE = 'm';

    public string $component = 'trend-metric';

    public string|null $defaultRange;

    public string|null $dateFormat = 'LLL. yy';

    protected string|null $prefix = null;

    protected string|null $suffix = null;

    public function countByMonths($request, string|Builder $model, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'count', null, static::UNIT_MONTH, $dateField);
    }

    public function countByWeeks($request, string|Builder $model, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'count', null, static::UNIT_WEEK, $dateField);
    }

    public function countByDays($request, string|Builder $model, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'count', null, static::UNIT_DAY, $dateField);
    }

    public function countByHours($request, string|Builder $model, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'count', null, static::UNIT_HOUR, $dateField);
    }

    public function countByMinutes($request, string|Builder $model, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'count', null, static::UNIT_MINUTE, $dateField);
    }

    public function avgByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'avg', $field, static::UNIT_MONTH, $dateField);
    }

    public function avgByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'avg', $field, static::UNIT_WEEK, $dateField);
    }

    public function avgByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'avg', $field, static::UNIT_DAY, $dateField);
    }

    public function avgByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'avg', $field, static::UNIT_HOUR, $dateField);
    }

    public function avgByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'avg', $field, static::UNIT_MINUTE, $dateField);
    }

    public function sumByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'sum', $field, static::UNIT_MONTH, $dateField);
    }

    public function sumByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'sum', $field, static::UNIT_WEEK, $dateField);
    }

    public function sumByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'sum', $field, static::UNIT_DAY, $dateField);
    }

    public function sumByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'sum', $field, static::UNIT_HOUR, $dateField);
    }

    public function sumByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'sum', $field, static::UNIT_MINUTE, $dateField);
    }

    public function maxByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'max', $field, static::UNIT_MONTH, $dateField);
    }

    public function maxByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'max', $field, static::UNIT_WEEK, $dateField);
    }

    public function maxByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'max', $field, static::UNIT_DAY, $dateField);
    }

    public function maxByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'max', $field, static::UNIT_HOUR, $dateField);
    }

    public function maxByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'max', $field, static::UNIT_MINUTE, $dateField);
    }

    public function minByMonths($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'min', $field, static::UNIT_MONTH, $dateField);
    }

    public function minByWeeks($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'min', $field, static::UNIT_WEEK, $dateField);
    }

    public function minByDays($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'min', $field, static::UNIT_DAY, $dateField);
    }

    public function minByHours($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'min', $field, static::UNIT_HOUR, $dateField);
    }

    public function minByMinutes($request, $model, string|Builder $field, string $dateField = null): TrendResult
    {
        return $this->aggregateBy($request, $model, 'min', $field, static::UNIT_MINUTE, $dateField);
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

    public function getProps(): array
    {
        return [
            ...parent::getProps(),
            'defaultRange' => $this->getDefaultRange(),
            'ranges' => collect($this->ranges())->map(fn ($range, $key) => ['key' => $key, 'name' => $range])->values(),
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
        ];
    }

    public function getDateFormat($range): string
    {
        return $this->dateFormat;
    }

    public function dateFormat(string $format): static
    {
        $this->dateFormat = $format;

        return $this;
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
        $timeFunc = match ($timeUnit) {
            static::UNIT_MONTH => 'subMonths',
            static::UNIT_WEEK => 'subWeeks',
            static::UNIT_DAY => 'subDays',
            static::UNIT_HOUR => 'subHours',
            static::UNIT_MINUTE => 'subMinutes',
            default => throw new \InvalidArgumentException('Invalid time unit'),
        };

        return [now()->{$timeFunc}($range), now()];

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

        $timezone = Pano::resolveUserTimezone($request) ?? $request->timezone;

        $builder = $query->whereBetween(
            $dateField,
            $this->currentRange($request->input('range'), $timeUnit, $timezone)
        )
        ;

        if ($builder instanceof \Illuminate\Database\Eloquent\Builder) {
            $field ??= '*';
            $trend = $builder
                ->select(DB::raw("DATE({$dateField}) as date"), DB::raw("{$function}({$field}) as value"))
                ->groupBy('date')
                ->get()
                ->keyBy('date')
                ->map(fn ($a) => $a->value)
                ->all()
            ;
        } elseif ($builder instanceof \Elastico\Eloquent\Builder) {
            $trend = $builder->addAggregation(
                (new DateHistogram('trend'))->field($dateField)->calendarInterval('1'.$timeUnit)->format($this->getDateFormat($request->range))
                    ->when('count' !== $function, fn ($hist) => $hist->addAggregation($this->getAggregation($function, $field)))
            )
                ->take(0)

                ->get()
                ->aggregation('trend')
                ->buckets()
                ->keyBy(fn ($bucket) => $bucket['key_as_string'])
                ->map(fn ($bucket) => 'count' === $function ? $bucket['doc_count'] : $bucket['agg']['value'])
                ->all()
            ;
        }

        return (new TrendResult())->trend($trend);
    }
}
