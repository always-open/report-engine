<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

class YesNoShort extends YesNo
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function format($value, ?object $result = null)
    {
        return $value ? 'Y' : 'N';
    }
}
