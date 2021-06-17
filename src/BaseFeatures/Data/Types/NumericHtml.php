<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\Number as BaseNumber;

class NumericHtml extends BaseNumber
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
     * @return string
     */
    public function formatter() : string
    {
        return 'html';
    }
}
