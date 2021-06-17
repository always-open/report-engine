<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Traits;

use Illuminate\Support\Collection;

trait ColumnsMappable
{
    /**
     * @param array $fields
     *
     * @return Collection
     */
    public function mapFieldsToColumns(array $fields): Collection
    {
        $columns = $this->columns->mapWithKeys(function ($column) {
            return [
                $column->name() => $column,
            ];
        });

        return collect(array_keys($fields))
            ->filter(function ($field) use ($columns) {
                return in_array($field, $columns->keys()->toArray());
            })->mapWithKeys(function ($field) use ($columns) {
                return [
                    trim($field) => $columns[$field],
                ];
            });
    }
}
