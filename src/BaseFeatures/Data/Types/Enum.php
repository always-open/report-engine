<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use Illuminate\Support\Collection;

class Enum extends BaseType
{
    protected $options;
    protected $prepend_all = true;
    protected $default;
    protected $use_keys = false;

    /**
     * Enum constructor.
     *
     * @param iterable $options
     * @param null     $default
     */
    public function __construct(iterable $options, $default = null)
    {
        if (is_array($options)) {
            $options = collect($options)->map(function ($label, $value) {
                return [
                    'label' => $label,
                    'value' => $value,
                ];
            });
        }

        $this->default = $default;

        $this->options = collect($options);
    }

    /**
     * @param bool $useKey
     *
     * @return $this
     */
    public function setUseKey(bool $useKey = false) : self
    {
        $this->use_keys = $useKey;

        return $this;
    }

    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return string
     */
    public function typeFormat($value, ?object $result = null)
    {
        return (string) $value;
    }

    /**
     * @return Enum
     */
    public function excludeAllOption(): self
    {
        $this->prepend_all = false;

        return $this;
    }

    /**
     * @return Enum
     */
    public function includeAllOption(): self
    {
        $this->prepend_all = true;

        return $this;
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
        ];
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        if ($this->prepend_all) {
            $this->options
                ->prepend([
                    'label' => 'All',
                    'value' => '',
                ]);
        }

        return array_values($this->options->toArray());
    }

    /**
     * @param string   $label
     * @param string   $name
     * @param array    $action_types
     * @param BaseType $columnType
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderFilter(string $label, string $name, array $action_types, BaseType $columnType, Collection $value)
    {
        return view('report-engine::partials.enum-filter')->with([
            'label' => $label,
            'field' => $name,
            'options' => $this->options,
            'value' => $value,
            'useKey' => $this->use_keys,
        ]);
    }
}
