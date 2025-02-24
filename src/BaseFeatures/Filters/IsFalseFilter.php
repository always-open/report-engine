<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class IsFalseFilter extends BaseFilter
{
    /**
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    #[\Override]
    public function apply(Builder $builder, array $options = []) : Builder
    {
        $action = $this->getAction();

        return $builder->$action($this->getField(), 0);
    }

    /**
     * Label of filters being passed into report displayed to users.
     *
     * @return string
     */
    #[\Override]
    public static function label(): string
    {
        return 'is false';
    }

    /**
     * Key of filters, used in query string.
     *
     * @return string
     */
    #[\Override]
    public static function key(): string
    {
        return 'is_false';
    }
}
