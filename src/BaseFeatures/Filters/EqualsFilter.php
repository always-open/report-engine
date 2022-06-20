<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

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
            $value = Carbon::parse($this->getValue());

            if ($timeZoneString = Arr::get($options, 'timezone')) {
                $value->shiftTimezone($timeZoneString);
            }

            $greaterThanEqual = new GreaterThanOrEqualFilter($this->getColumn(), $value);
            $lessThanEqual = new LessThanOrEqualFilter($this->getColumn(), $value);
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
