<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\ContainsFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotContainFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\InFilter;

class Text extends BaseType
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    #[\Override]
    public function typeFormat($value, ?object $result = null)
    {
        if ($this->formatClosure) {
            return ($this->formatClosure)($value);
        }

        return (string) $value;
    }

    /**
     * Filters this data type can utilize.
     *
     * @return array
     */
    #[\Override]
    public static function availableFilters(): array
    {
        return [
            ContainsFilter::class,
            DoesNotContainFilter::class,
            EqualsFilter::class,
            DoesNotEqualFilter::class,
            InFilter::class,
        ];
    }
}
