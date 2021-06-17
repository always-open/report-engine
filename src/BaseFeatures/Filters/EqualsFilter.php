<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class EqualsFilter extends BaseFilter
{
    /**
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    public function apply(Builder $builder, array $options = []) : Builder
    {
        if ($this->valueIsDate()) {
            $greaterThanEqual = new GreaterThanOrEqualFilter($this->getColumn(), $this->getValue());
            $lessThanEqual = new LessThanOrEqualFilter($this->getColumn(), $this->getValue());
            $builder = $greaterThanEqual->apply($builder);

            return $lessThanEqual->apply($builder);
        }

        $action = $this->getAction();

        return $builder->$action($this->getField(), $this->getValue());
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return '= equals';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'equals';
    }
}
