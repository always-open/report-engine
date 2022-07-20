<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ContainsFilter extends BaseFilter
{
    /**
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    public function apply(Builder $builder, array $options = []) : Builder
    {
        return self::build($builder, $this->getField(), $this->getValue(), $this->getAction());
    }

    /**
     * @param Builder $builder
     * @param mixed   $field
     * @param mixed   $value
     * @param string  $action
     *
     * @return Builder
     */
    public static function build(Builder $builder, mixed $field, mixed $value, string $action = 'where') : Builder
    {
        return $builder->$action(DB::raw('COALESCE(' . $field . ", '')"), 'like', '%' . $value . '%');
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return '⊃ contains';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'contains';
    }
}
