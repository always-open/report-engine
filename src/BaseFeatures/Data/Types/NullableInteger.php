<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\NullableNumber;

class NullableInteger extends NullableNumber
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

        return $this->numberFormat((int) $value);
    }
}
