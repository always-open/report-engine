<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\Number as BaseNumber;

class NumericHtml extends BaseNumber
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

    public function formatter() : string|null
    {
        return 'html';
    }
}
