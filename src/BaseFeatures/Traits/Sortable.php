<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Traits;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Column;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\DateTime;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Dollar;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Integer;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\NullableDecimal;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\NullableInteger;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Number;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Percentage;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Text;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait Sortable
{
    protected $sorting = [];
    protected $sub_sorting = [];

    public function initSorting()
    {
        $this->determineSorting($this->getCurrentRequest()->get('sort_by', []));
    }

    /**
     * @param array $params
     *
     * @return self
     */
    public function determineSorting(array $params): self
    {
        $sortable = $this->sortableColumns($this->columns);

        $mapped_columns = $this->mapFieldsToColumns($params);

        $column_map = function ($column) use ($params) {
            return ($params[$column->name()] == 'desc') ? 'desc' : 'asc';
        };

        $is_subquery = false;
        $filter_closure = function ($column, $param) use ($sortable, &$is_subquery) {
            return $sortable->contains($param)
                && $column->isSubquery() === $is_subquery;
        };

        $this->sorting = $mapped_columns
            ->filter($filter_closure)
            ->map($column_map);

        $is_subquery = true; // called in use within closure

        $this->sub_sorting = $mapped_columns
            ->filter($filter_closure)
            ->map($column_map);

        return $this;
    }

    /**
     * @param Builder   $builder
     * @param bool|null $is_subquery
     *
     * @return Builder
     */
    public function applySorting(Builder $builder, ?bool $is_subquery = false) : Builder
    {
        return $this->scopeSortableBy($builder, $is_subquery ? collect($this->sub_sorting) : collect($this->sorting));
    }

    /**
     * @param Builder    $builder
     * @param Collection $sortableColumns
     *
     * @return Builder
     */
    public function scopeSortableBy(Builder $builder, Collection $sortableColumns) : Builder
    {
        $defaultSort = collect(with($builder instanceof Builder ? $builder : $builder->getQuery())->orders)
            ->mapWithKeys(function ($order_by) {
                return [
                    data_get($order_by, 'column', data_get($order_by, 'sql')) => [
                        'type'      => data_get($order_by, 'type'),
                        'direction' => data_get($order_by, 'direction', 'asc'),
                    ],
                ];
            });
        // Remove existing orderBy attributes
        with($builder instanceof Builder ? $builder : $builder->getQuery())->orders = null;

        $sortBy = $sortableColumns->isNotEmpty()
            ? $sortableColumns->mapWithKeys(function ($direction, $column) {
                return [$column => [
                    'direction' => $direction,
                ]];
            })
            : $defaultSort;

        $sortBy->each(function ($data, string $field) use (&$builder) {
            if (! empty($data) && ! is_array($data)) {
                $data = Arr::wrap($data);
            }
            $column = null;
            $direction = data_get($data, 'direction');

            if (data_get($data, 'type') === 'Raw') {
                $order_by = $field;

                if (preg_match('/\((.+?)\)/', $field, $fields)) {
                    $field = data_get($fields, 1);
                    $column = $this->columns
                        ->filter(function (Column $column) use ($field) {
                            // name is protected, need to access via function
                            return $column->name() === $field;
                        })
                        ->first();
                }
            } else {
                $column = $this->columns
                    ->filter(function (Column $column) use ($field) {
                        // name is protected, need to access via function
                        return $column->name() === $field;
                    })
                    ->first();
                $order_by = $column ? $column->aliasSortOrName() : $field;
            }

            // Make sure that nulls are always at the bottom
            switch (optional(optional($column)->type())->name()) {
                case strtolower(NullableInteger::class):
                case strtolower(NullableDecimal::class):
                case strtolower(Number::class):
                case strtolower(Integer::class):
                case strtolower(Percentage::class):
                case strtolower(Dollar::class):
                    $coalesce_value = ($direction == 'desc' ? 0 : 9999999999);

                    break;

                case strtolower(DateTime::class):
                    $coalesce_value = "''";

                    break;

                case strtolower(Text::class):
                default:
                    $coalesce_value = ($direction == 'desc' ? "''" : "'ZZ'");

                    break;
            }

            if (mb_strpos($order_by, ' ')) {
                $order_by = "\"$order_by\"";
            }

            if (optional(optional($column)->type())->shouldUseCoalesce() ?? true) {
                $order_by = \DB::raw("COALESCE($order_by, $coalesce_value)");
            }

            $builder->orderBy($order_by, $direction ?? 'asc');
        });

        return $builder;
    }

    /**
     * @param Collection $columns
     *
     * @return Collection
     */
    public function sortableColumns(Collection $columns): Collection
    {
        return $columns->map(function (Column $column) {
            return $column->name();
        });
    }

    /**
     * @return bool
     */
    public function usesSorting(): bool
    {
        return true;
    }

    /**
     * @param array $sorting
     *
     * @return $this
     */
    public function setSorting(array $sorting): self
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCurrentlySortedBy(): Collection
    {
        return collect($this->sorting);
    }
}
