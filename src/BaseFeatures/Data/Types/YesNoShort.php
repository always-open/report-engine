<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

class YesNoShort extends YesNo
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null)
    {
        return $value ? 'Y' : 'N';
    }
}
