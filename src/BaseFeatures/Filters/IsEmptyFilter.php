<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class IsEmptyFilter extends BaseFilter
{
    /**
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    public function apply(Builder $builder, array $options = []) : Builder
    {
        /**
         * @psalm-suppress ImplicitToStringCast
         */
        return $builder->whereNull($this->getField());
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return 'is empty';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'is_empty';
    }
}
