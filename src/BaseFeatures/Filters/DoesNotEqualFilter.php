<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class DoesNotEqualFilter extends BaseFilter
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

        if ($this->valueIsDate()) {
            return $builder->where(function (Builder $builder) {
                $builder->where((string) $this->getField(), '>', $this->getValue()->endOfDay()->toDateTimeString())
                    ->orWhere((string) $this->getField(), '<', $this->getValue()->startOfDay()->toDateTimeString());
            });
        }

        return $builder->$action($this->getField(), '!=', $this->getValue());
    }

    /**
     * Label of filters being passed into report displayed to users.
     *
     * @return string
     */
    public static function label(): string
    {
        return '≠ does not equal';
    }

    /**
     * Key of filters, used in query string.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'does_not_equal';
    }

    /**
     * @return null|string|Carbon
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

            return $value->utc();
        }

        return parent::getValue();
    }
}
