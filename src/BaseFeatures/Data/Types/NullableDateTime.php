<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsEmptyFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsNotEmptyFilter;
use Illuminate\Support\Collection;

class NullableDateTime extends DateTime
{
    public function __construct(?string $date_time_format = null, ?string $placeholder = null, ?string $output_tz_name = null)
    {
        parent::__construct($date_time_format, $placeholder, $output_tz_name);

        $this->clearClasses();
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
        return view('report-engine::partials.empty-not-empty-filter')->with([
            'label' => $label,
            'field' => $name,
            'options' => collect($this->getOptions()),
            'value' => $value,
            'useKey' => true,
        ]);
    }

    public static function availableFilters(): array
    {
        return [
            IsEmptyFilter::class,
            IsNotEmptyFilter::class,
        ];
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
