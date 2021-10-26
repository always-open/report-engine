<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases;

use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsEmptyFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsNotEmptyFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;

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
