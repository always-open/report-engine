<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class GreaterThanOrEqualFilter extends BaseFilter
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
        $value = $this->getValue();

        if ($this->valueIsDate()) {
            /**
             * @var Carbon $value
             */
            if ($timeZoneString = Arr::get($options, 'timezone')) {
                $value->shiftTimezone($timeZoneString);
            }

            $value = $value->utc()->toDateTimeString();
        }

        return $builder->$action($this->getField(), '>=', $value);
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        if ($this->valueIsDate()) {
            return parent::getValue()->startOfDay();
        }

        return parent::getValue();
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return '≥ greater than or equal to';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'greater_than_or_equal';
    }
}
