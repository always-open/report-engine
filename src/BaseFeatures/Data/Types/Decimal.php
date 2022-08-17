<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\Number as BaseNumber;

class Decimal extends BaseNumber
{
    /**
     * @var int
     */
    private int $decimal_places;

    /**
     * @var bool
     */
    protected bool $with_separator = true;

    /**
     * Decimal constructor.
     *
     * @param int $decimal_places
     */
    public function __construct(int $decimal_places = 2)
    {
        $this->decimal_places = $decimal_places;
    }

    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null) : string
    {
        return $this->numberFormat((float) $value, $this->decimal_places);
    }
}
