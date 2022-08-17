<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class IsTrueFilter extends BaseFilter
{
    /**
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    public function apply(Builder $builder, array $options = []) : Builder
    {
        $action = $this->getAction();

        return $builder->$action($this->getField(), 1);
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return 'is true';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'is_true';
    }
}
