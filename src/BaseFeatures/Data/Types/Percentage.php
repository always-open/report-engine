<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\Number as BaseNumber;

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
    public function format($value, ?object $result = null)
    {
        return $this->numberFormat($value, 1) . '%';
    }
}
