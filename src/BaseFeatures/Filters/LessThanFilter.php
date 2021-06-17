<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class LessThanFilter extends BaseFilter
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

        return $builder->$action($this->getField(), '<', $this->getValue());
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        if ($this->valueIsDate()) {
            return parent::getValue()->startOfDay()->toDateTimeString();
        }

        return parent::getValue();
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return '< less than';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'less_than';
    }
}
