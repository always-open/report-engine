<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Filters;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Column;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\DateTime;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Carbon;

abstract class BaseFilter implements Arrayable
{
    /**
     * @var Column
     */
    protected Column $column;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * BaseFilter constructor.
     *
     * @param Column       $column
     * @param mixed|null   $value
     */
    public function __construct(Column $column, mixed $value = null)
    {
        $this->setColumn($column);
        $this->setValue($value);
    }

    /**
     * Logic to apply the filter to the report query.
     *
     * @param Builder $builder
     * @param array   $options
     *
     * @return Builder
     */
    abstract public function apply(Builder $builder, array $options = []) : Builder;

    /**
     * Label of filters being passed into report displayed to users.
     *
     * @return string
     */
    abstract public static function label(): string;

    /**
     * Key of filters, used in query string.
     *
     * @return string
     */
    abstract public static function key(): string;

    /**
     * Can this filter be applied multiple times to the same query?
     *
     * @return bool
     */
    public static function allowMultiple(): bool
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;

        if ($this->valueIsDate()) {
            $this->value = Carbon::parse($value);
        }
    }

    /**
     * @return bool
     */
    public function valueIsDate(): bool
    {
        if (! $this->value) {
            return false;
        }

        return $this->column->type() instanceof DateTime;
    }

    /**
     * @return string|Expression
     */
    public function getField()
    {
        $alias_or_name = $this->column->aliasOrName();

        if ($alias_or_name instanceof Expression) {
            return $alias_or_name;
        }

        return (string) $alias_or_name;
    }

    /**
     * @param Column $column
     */
    public function setColumn(Column $column): void
    {
        $this->column = $column;
    }

    /**
     * @return Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->getColumn()->action();
    }

    public function toArray() : array
    {
        return [
            'value' => $this->getValue(),
            'field' => $this->getField(),
            'label' => $this->label(),
            'key' => $this->key(),
        ];
    }

    protected static function escapeFieldName(string $field) : string
    {
        return '`' . implode('`.`', explode('.', $field)) . '`';
    }
}
