<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Traits;

use AlwaysOpen\ReportEngine\BaseFeatures\Filters\BaseFilter;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

trait Filterable
{
    /**
     * @var Collection|null
     */
    protected ?Collection $appliedFilters = null;

    /**
     * @param array   $params
     * @param Builder $builder
     *
     * @return Builder
     */
    protected function applyFilters(array $params, Builder $builder) : Builder
    {
        $options = $this->getMetaData();

        $this->getAppliedfilters($params)
            ->each(function ($fields) use ($options, &$builder) {
                $fields->each(function ($filter) use ($options, &$builder) {
                    $builder = $this->filter($filter, $builder, $options);
                });
            });

        return $builder;
    }

    /**
     * @param BaseFilter $filter
     * @param Builder    $builder
     * @param array      $options
     *
     * @return Builder
     */
    protected function filter(BaseFilter $filter, Builder $builder, array $options = []) : Builder
    {
        return $filter->apply($builder, $options);
    }

    /**
     * @param array  $filterClasses
     * @param string $key
     *
     * @return string|null
     */
    public static function matchFilter(array $filterClasses, string $key): ?string
    {
        return collect($filterClasses)->filter(function ($class) use ($key) {
            return $class::key() == $key;
        })->first();
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getCurrentlyFilteredBy(array $params): Collection
    {
        return collect($this->getAppliedfilters($params))
            ->mapWithKeys(function ($filters, $key) {
                /** @var Collection $filters */
                return [
                    $key => $filters->map(function ($filter) {
                        /** @var \AlwaysOpen\ReportEngine\BaseFeatures\Filters\BaseFilter $filter */
                        return $filter->getValue();
                    }),
                ];
            });
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getAppliedfilters(array $params) : Collection
    {
        if (empty($this->appliedFilters)) {
            $cols = $this->mapFieldsToColumns($params);

            /**
             * @psalm-suppress UnusedClosureParam
             */
            $this->appliedFilters = collect($params)
                ->filter(function ($filters, $field) use ($cols) {
                    return $cols[$field] ?? null;
                })
                ->map(function ($filters) {
                    return collect($filters)->mapWithKeys(function ($value, $filter) {
                        return [trim($filter) => trim($value)];
                    });
                })
                ->map(function ($filters, $field) use ($cols) {
                    $column = $cols[$field];
                    $filterClasses = $column->filters();

                    if (empty($filterClasses)) {
                        return collect();
                    }

                    return collect($filters)->map(function ($value, $filter) use ($filterClasses, $column) {
                        $class = static::matchFilter($filterClasses, $filter);

                        if ($class) {
                            return new $class($column, $value);
                        }

                        return null;
                    });
                });
        }

        return $this->appliedFilters;
    }
}
