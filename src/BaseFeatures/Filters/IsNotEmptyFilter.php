<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class IsNotEmptyFilter extends BaseFilter
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
        return $builder->whereNotNull($this->getField());
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return 'is not empty';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'is_not_empty';
    }
}
