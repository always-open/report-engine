<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases;

use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsEmptyFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsNotEmptyFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;

abstract class NullableNumber extends Number
{
    public function __construct($default_value = null)
    {
        $this->default_value = $default_value;
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
            IsEmptyFilter::class,
            IsNotEmptyFilter::class,
            LessThanFilter::class,
            LessThanOrEqualFilter::class,
        ];
    }
}
