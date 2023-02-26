<?php

namespace Pano\Metrics\Custom;

use Elastico\Aggregations\Aggregation;
use Elastico\Aggregations\Bucket\Filter;
use Elastico\Aggregations\Bucket\SignificantText as SignificantTextAggregation;
use Elastico\Aggregations\Metric\Avg;
use Elastico\Aggregations\Metric\Max;
use Elastico\Aggregations\Metric\Min;
use Elastico\Aggregations\Metric\Sum;
use Elastico\Query\Builder;
use Elastico\Query\MatchAll;
use Pano\Facades\Pano;
use Pano\Metrics\Partition;
use Pano\Metrics\Results\PartitionResult;

/**
 *  Metric.
 */
abstract class SignificantText extends Partition
{
    const TYPE = 'partition';

    protected string|null $prefix = null;

    protected string|null $suffix = null;

    protected int $size = 5;

    protected string $type = 'gnd';

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

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function config(): array
    {
        return [
            ...parent::config(),
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

        $timezone = Pano::resolveUserTimezone($request) ?? $request->timezone;

        $results = $query->take(0)
            ->addAggregation(
                (new SignificantTextAggregation('terms'))
                    ->type($this->type)
                    ->field($by)
                    ->size($this->size)
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
