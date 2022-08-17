<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\ContainsFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotContainFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\InFilter;

class Html extends BaseType
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null)
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
            InFilter::class,
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
