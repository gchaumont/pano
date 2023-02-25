<?php

namespace Pano\Metrics;

use Elastico\Aggregations\Aggregation;
use Elastico\Aggregations\Bucket\Filter;
use Elastico\Aggregations\Bucket\Terms;
use Elastico\Aggregations\Metric\Avg;
use Elastico\Aggregations\Metric\Max;
use Elastico\Aggregations\Metric\Min;
use Elastico\Aggregations\Metric\Sum;
use Elastico\Query\MatchAll;
use Illuminate\Database\Eloquent\Builder;
use Pano\Facades\Pano;
use Pano\Metrics\Results\PartitionResult;

/**
 *  Metric.
 */
abstract class Partition extends Metric
{
    const TYPE = 'partition';

    public string $component = 'partition-metric';

    protected string|null $prefix = null;

    protected string|null $suffix = null;

    protected int $size = 5;

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

    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getProps(): array
    {
        return [
            ...parent::getProps(),
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

        if (is_a($query->getModel(), \Elastico\Eloquent\Model::class)) {
            $results = $query
                ->take(0)
                ->select()
                ->addAggregation(
                    (new Terms('terms'))->field($by)->size($this->size)->addAggregation($this->getAggregation($function, $field))
                )
                ->when(!empty($callback), fn ($q) => $callback($q))
                ->get()
                ->aggregation('terms')
                ->buckets()
                ->map(fn ($bucket) => [
                    'name' => $bucket['key'],
                    'id' => $bucket['key'],
                    'value' => 'count' == $function ? $bucket['doc_count'] : $bucket['agg']['value'],
                ])
                ->all()
            ;
        } elseif (is_a($query->getModel(), \Illuminate\Database\Eloquent\Model::class)) {
            $field ??= '*';
            $results = $query
                ->groupBy($by)
                ->when(!empty($callback), fn ($q) => $callback($q))

                ->orderByRaw("{$function}({$field}) DESC")
                ->selectRaw("{$function}({$field}) as metric, {$by}")
                ->take(5)
                ->pluck('metric', $by)
                ->map(fn ($result, $key) => [
                    'name' => $key,
                    'id' => $key,
                    'value' => $result,
                ])
                ->values()
                ->all()
            ;
        }

        $timezone = Pano::resolveUserTimezone($request) ?? $request->timezone;

        return (new PartitionResult())->partition($results)->field($by);
    }
}
