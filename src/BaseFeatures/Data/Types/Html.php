<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\ContainsFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotContainFilter;

class Html extends BaseType
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function format($value, ?object $result = null)
    {
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
            ContainsFilter::class,
            DoesNotContainFilter::class,
        ];
    }

    /**
     * @return string
     */
    public function formatter() : string
    {
        return 'html';
    }
}
