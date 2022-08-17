<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsFalseFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsTrueFilter;
use Illuminate\Support\Collection;

class YesNo extends BaseType
{
    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null)
    {
        return $value ? 'Yes' : 'No';
    }

    /**
     * Filters this data type can utilize.
     *
     * @return array
     */
    public static function availableFilters(): array
    {
        return [
            IsTrueFilter::class,
            IsFalseFilter::class,
        ];
    }

    /**
     * @param string     $label
     * @param string     $name
     * @param array      $action_types
     * @param BaseType   $columnType
     * @param Collection $value
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function renderFilter(string $label, string $name, array $action_types, BaseType $columnType, Collection $value)
    {
        return view('report-engine::partials.yes-no-filter')->with([
            'label' => $label,
            'field' => $name,
            'options' => collect($this->getOptions()),
            'value' => $value,
            'useKey' => true,
        ]);
    }

    public function inputType() : string
    {
        return 'select';
    }

    public function getOptions() : array
    {
        return [
            '1' => 'Yes',
            '0' => 'No',
        ];
    }
}
