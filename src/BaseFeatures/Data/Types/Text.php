<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\ContainsFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotContainFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\EqualsFilter;

class Text extends BaseType
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
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
    public static function availableFilters(): array
    {
        return [
            ContainsFilter::class,
            DoesNotContainFilter::class,
            EqualsFilter::class,
            DoesNotEqualFilter::class,
        ];
    }
}
