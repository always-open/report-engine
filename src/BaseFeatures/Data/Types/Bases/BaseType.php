<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases;

use Illuminate\Support\Collection;

abstract class BaseType
{
    /**
     * @var mixed
     */
    protected $default = null;

    protected string $styleClasses = '';

    protected string $placeholder = '';

    protected ?\Closure $formatClosure = null;

    /**
     * @var null|string
     */
    protected ?string $formatter = null;

    /**
     * @param mixed       $value
     * @param object|null $result
     *
     * @return mixed
     */
    public function format($value, ?object $result = null)
    {
        if ($this->formatClosure) {
            $value = ($this->formatClosure)($value);
        }

        return $this->typeFormat($value, $result);
    }

    abstract public function typeFormat($value, ?object $result = null);

    /**
     * Filters this data type can utilize.
     *
     * @return array
     */
    abstract public static function availableFilters(): array;

    public function setFormatFunction(?\Closure $formatClosure):self
    {
        $this->formatClosure = $formatClosure;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function exportFormat($value)
    {
        return $this->format($value);
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return strtolower(class_basename($this));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function inputType() : string
    {
        return 'text';
    }

    /**
     * @param string $class
     */
    public function addClass(string $class)
    {
        $this->styleClasses .= $class;
    }

    /**
     * @return string
     */
    public function styleClass() : string
    {
        return $this->styleClasses;
    }

    /**
     * @return string
     */
    public function placeholder() : string
    {
        return $this->placeholder;
    }

    /**
     * @param string      $label
     * @param string      $name
     * @param array       $actionTypes
     * @param self        $columnType
     * @param Collection  $value
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function renderFilter(string $label, string $name, array $actionTypes, self $columnType, Collection $value)
    {
        return view('report-engine::partials.base-filter')->with([
            'label' => $label,
            'field' => $name,
            'value' => $value,
            'actionTypes' => $actionTypes,
            'inputType' => $columnType->inputType(),
            'classes' => $this->styleClass(),
            'placeholder' => $this->placeholder(),
        ]);
    }

    /**
     * @return array
     */
    public function formatterParams() : array
    {
        return [];
    }

    /**
     * @param string|null $formatter
     *
     * @return $this
     */
    public function setFormatter(?string $formatter) : self
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @return string
     */
    public function formatter() : string
    {
        return $this->formatter ?? 'plaintext';
    }

    public function getOptions() : array
    {
        return [];
    }
}
