<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class DoesNotEqualFilter extends BaseFilter
{
    /**
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    public function apply(Builder $builder, array $options = []) : Builder
    {
        $action = $this->getAction();

        return $builder->$action($this->getField(), '!=', $this->getValue());
    }

    /**
     * Label of filters being passed into report displayed to users.
     *
     * @return string
     */
    public static function label(): string
    {
        return '≠ does not equal';
    }

    /**
     * Key of filters, used in query string.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'does_not_equal';
    }
}
