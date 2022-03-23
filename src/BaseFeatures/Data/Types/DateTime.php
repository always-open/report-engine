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
    protected ?string $outputTzName = null;

    protected ?string $inputFormat = 'YYYY-MM-DD HH:ii:ss';

    protected string $outputFormat = 'L';

    public function __construct(
        ?string $outputFormat = null,
        ?string $placeholder = null,
        ?string $outputTimezone = null
    ) {
        $this->setOutputFormat($outputFormat ?? $this->outputFormat)
            ->setOutputTimezone($outputTimezone)
            ->setPlaceholder($placeholder ?? $this->placeholder);
        $this->addClass('datepicker');
    }

    public function setOutputFormat(?string $format) : self
    {
        $this->outputFormat = $format;

        return $this;
    }

    public function setPlaceholder(string $placeholder) : self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function setInputFormat(?string $format) : self
    {
        $this->inputFormat = $format;

        return $this;
    }

    public function setOutputTimezone(?string $timezone) : self
    {
        $this->outputTzName = $timezone;

        return $this;
    }

    public function formatter(): string
    {
        return 'datetime';
    }

    public function formatterParams(): array
    {
        $params = [
            'outputFormat' => $this->outputFormat,
        ];

        if ($this->inputFormat) {
            $params['inputFormat'] = $this->inputFormat;
        }

        if ($this->outputTzName) {
            $params['timezone'] = $this->outputTzName;
        }

        return $params;
    }

    public function inputType() : string
    {
        return 'date';
    }

    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return ?string
     */
    public function typeFormat($value, ?object $result = null) : ?string
    {
        return (new Carbon($value))->toDateTimeString(); //->setTimezone($this->outputTzName)->format($this->outputFormat);
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

    /**
     * @param string     $label
     * @param string     $name
     * @param array      $action_types
     * @param BaseType   $columnType
     * @param Collection $value
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function renderFilter(string $label, string $name, array $action_types, BaseType $columnType, Collection $value)
    {
        $value = $value->map(function ($value) {
            return Carbon::parse($value)->isoFormat($this->outputFormat);
        });

        return view('report-engine::partials.date-filter')->with([
            'label' => $label,
            'field' => $name,
            'value' => $value,
            'action_types' => $action_types,
            'input_type' => $columnType->inputType(),
            'classes' => $this->styleClass(),
            'placeholder' => $this->placeholder(),
            'selected_operators' => $this->getSelectedOperators($value),
        ]);
    }
}
