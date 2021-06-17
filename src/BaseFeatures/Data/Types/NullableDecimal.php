<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\NullableNumber;

class NullableDecimal extends NullableNumber
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function format($value, ?object $result = null)
    {
        if (null === $value || $this->default_value === $value) {
            return '--';
        }

        return $this->numberFormat($value, 2);
    }
}
