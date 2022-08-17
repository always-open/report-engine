<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Filters;

use Illuminate\Database\Query\Builder;

class InFilter extends BaseFilter
{
    /**
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    public function apply(Builder $builder, array $options = []) : Builder
    {
        $options = collect(explode(',', $this->getValue()))->map(fn ($s) => trim($s));

        return $builder->whereIn((string) $this->getField(), $options);
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return '() in';
    }

    /**
     * @return string
     */
    public static function key(): string
    {
        return 'in';
    }
}
