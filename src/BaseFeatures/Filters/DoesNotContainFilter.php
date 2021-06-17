<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class DoesNotContainFilter extends BaseFilter
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

        return $builder->$action($this->getField(), 'not like', '%' . $this->getValue() . '%');
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return '⊄ does not contain';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'does_not_contain';
    }
}
