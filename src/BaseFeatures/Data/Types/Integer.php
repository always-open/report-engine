<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\Number as BaseNumber;

class Integer extends BaseNumber
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
        return $this->numberFormat((int) $value);
    }
}
