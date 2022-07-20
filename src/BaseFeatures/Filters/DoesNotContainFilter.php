<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

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
        return $builder->$action(DB::raw('COALESCE(' . $field . ", '')"), 'not like', '%' . $value . '%');
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
