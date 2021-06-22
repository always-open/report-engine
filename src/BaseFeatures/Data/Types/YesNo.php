<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsFalseFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsTrueFilter;

class YesNo extends BaseType
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null)
    {
        return $value ? 'Yes' : 'No';
    }

    /**
     * Filters this data type can utilize.
     *
     * @return array
     */
    public static function availableFilters(): array
    {
        return [
            IsTrueFilter::class,
            IsFalseFilter::class,
        ];
    }
}
