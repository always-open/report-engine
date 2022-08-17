<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Filters;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class LessThanFilter extends BaseFilter
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
        $value = $this->getValue($options);

        return $builder->$action($this->getField(), '<', $value);
    }

    /**
     * @return null|string
     */
    public function getValue(array $options = [])
    {
        if ($this->valueIsDate()) {
            /**
             * @var Carbon $value
             */
            $value = parent::getValue();
            $timeZoneString = $this->getColumn()->type()->getOutputTimezone()
                ?? Arr::get($options, 'timezone');

            if ($timeZoneString) {
                $value->shiftTimezone($timeZoneString);
            }

            return $value->startOfDay()->utc()->toDateTimeString();
        }

        return parent::getValue();
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return '< less than';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'less_than';
    }
}
