<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DateTime extends BaseType
{
    /**
     * @var null|string like "America/New_York".
     */
    protected $outputTzName = null;

    /**
     * @var string
     */
    protected $date_time_format = 'L';

    /**
     * DateTime constructor.
     *
     * @param string|null $output_tz_name
     * @param string|null $placeholder
     * @param string|null $date_time_format
     */
    public function __construct(?string $date_time_format = null, ?string $placeholder = null, ?string $output_tz_name = null)
    {
        $this->outputTzName = $output_tz_name;
        $this->date_time_format = $date_time_format ?? $this->date_time_format;
        $this->addClass('datepicker');
        $this->placeholder = $placeholder ?? $this->placeholder;
    }

    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string|null
     */
    public function typeFormat($value, ?object $result = null) : ?string
    {
        return (new Carbon($value))->toDateTimeString(); //->setTimezone($this->outputTzName)->format($this->date_time_format);
    }

    /**
     * Filters this data type can utilize.
     *
     * @return array
     */
    public static function availableFilters(): array
    {
        return [
            EqualsFilter::class,
            DoesNotEqualFilter::class,
            GreaterThanFilter::class,
            GreaterThanOrEqualFilter::class,
            LessThanFilter::class,
            LessThanOrEqualFilter::class,
        ];
    }

    public function formatter(): string
    {
        return 'datetime';
    }

    public function formatterParams(): array
    {
        return [
            'inputFormat' => 'YYYY-MM-DD HH:ii:ss',
            'outputFormat' => $this->date_time_format,
            'timezone' => $this->outputTzName ?? 'America/New_York',
        ];
    }

    /**
     * @param string     $label
     * @param string     $name
     * @param array      $actionTypes
     * @param BaseType   $columnType
     * @param Collection $value
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function renderFilter(string $label, string $name, array $actionTypes, BaseType $columnType, Collection $value)
    {
        $value = $value->map(function ($value) {
            return Carbon::parse($value)->isoFormat($this->date_time_format);
        });

        return view('report-engine::partials.date-filter')->with([
            'label'       => $label,
            'field'       => $name,
            'value'       => $value,
            'actionTypes' => $actionTypes,
            'inputType'   => $columnType->inputType(),
            'classes'     => $this->styleClass(),
            'placeholder' => $this->placeholder(),
        ]);
    }
}
