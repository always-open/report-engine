<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\NullableNumber;

class NullableDecimal extends NullableNumber
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null)
    {
        if (null === $value || $this->getDefaultValue() === $value) {
            return '--';
        }

        return $this->numberFormat($value, 2);
    }
}
