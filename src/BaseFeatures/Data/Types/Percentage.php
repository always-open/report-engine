<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\Number as BaseNumber;

class Percentage extends BaseNumber
{
    /**
     * @var bool
     */
    public bool $with_separator = true;

    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null)
    {
        return $this->numberFormat($value, 1) . '%';
    }
}
