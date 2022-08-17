<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases;

use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;

abstract class Number extends BaseType
{
    /**
     * @var bool
     */
    protected bool $with_separator = false;

    /**
     * @var string
     */
    protected string $separator = ',';

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator(string $separator) : self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableSeparator() : self
    {
        $this->with_separator = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableSeparator() : self
    {
        $this->with_separator = false;

        return $this;
    }

    /**
     * @param mixed $value
     * @param int   $decimals
     *
     * @return string
     */
    protected function numberFormat($value, int $decimals = 0) : string
    {
        if ($this->with_separator) {
            return number_format($value, $decimals, '.', $this->separator);
        }

        return (string) $value;
    }

    /**
     * Filters this data type can utilize.
     *
     * @return array
     */
    public static function availableFilters(): array
    {
        return [
            EqualsFilter::class,
            DoesNotEqualFilter::class,
            GreaterThanFilter::class,
            GreaterThanOrEqualFilter::class,
            LessThanFilter::class,
            LessThanOrEqualFilter::class,
        ];
    }

    /**
     * @return string
     */
    public function inputType() : string
    {
        return 'number';
    }
}
