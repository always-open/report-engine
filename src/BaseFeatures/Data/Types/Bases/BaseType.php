<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class BaseType
{
    /**
     * @var mixed
     */
    protected $default_value = null;

    protected array $default_comparison_operators = [];

    protected string $styleClasses = '';

    protected string $placeholder = '';

    protected ?\Closure $formatClosure = null;

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
        return $this->default_value;
    }

    /**
     * @param mixed $default_value
     *
     * @return $this
     */
    public function setDefaultValue($default_value) : self
    {
        $this->default_value = $default_value;

        return $this;
    }

    public function getDefaultComparisonOperators() : array
    {
        return $this->default_comparison_operators;
    }

    /**
     * @param string|array $operators
     */
    public function setDefaultComparisonOperators($operators) : self
    {
        $this->default_comparison_operators = Arr::wrap($operators);

        return $this;
    }

    /**
     * @param string|array $operators
     */
    public function addDefaultComparisonOperators($operators) : self
    {
        $this->default_comparison_operators += Arr::wrap($operators);

        return $this;
    }

    public function inputType() : string
    {
        return 'text';
    }

    public function clearClasses() : self
    {
        $this->styleClasses = '';

        return $this;
    }

    public function addClass(string $class)
    {
        $this->styleClasses .= $class;
    }

    public function styleClass() : string
    {
        return $this->styleClasses;
    }

    public function placeholder() : string
    {
        return $this->placeholder;
    }

    /**
     * @param string      $label
     * @param string      $name
     * @param array       $action_types
     * @param self        $columnType
     * @param Collection  $value
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function renderFilter(string $label, string $name, array $action_types, self $columnType, Collection $value)
    {
        return view('report-engine::partials.base-filter')
            ->with(
                $this->getConfig($label, $name, $action_types, $columnType, $value)
            );
    }

    public function getConfig(string $label, string $name, array $action_types, self $columnType, Collection $value) : array
    {
        return [
            'label' => $label,
            'field' => $name,
            'value' => $value,
            'action_types' => $action_types,
            'default_value' => $this->default_value,
            'input_type' => $columnType->inputType(),
            'classes' => $this->styleClass(),
            'placeholder' => $this->placeholder(),
            'selected_operators' => $this->getSelectedOperators($value),
            'options' => $this->getOptions(),
            'use_keys' => $this->use_keys ?? false,
        ];
    }

    protected function getSelectedOperators(Collection $values) : array
    {
        return $values->keys()->all() + $this->getDefaultComparisonOperators();
    }

    public function formatterParams() : array
    {
        return [];
    }

    public function setFormatter(?string $formatter) : self
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function formatter() : string
    {
        return $this->formatter ?? 'plaintext';
    }

    public function getOptions() : array
    {
        return [];
    }
}
