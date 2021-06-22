<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\ContainsFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotContainFilter;

class Url extends BaseType
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function typeFormat($value, ?object $result = null)
    {
        return url($value);
    }

    /**
     * Filters this data type can utilize.
     *
     * @return array
     */
    public static function availableFilters(): array
    {
        return [
            ContainsFilter::class,
            DoesNotContainFilter::class,
        ];
    }
}
