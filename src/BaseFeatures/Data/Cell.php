<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use Throwable;

class Cell
{
    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @var mixed
     */
    protected $raw_value = null;

    /**
     * @var Column
     */
    protected Column $column;

    /**
     * @var string|null
     */
    protected ?string $tooltip = null;

    /**
     * @var string|null
     */
    protected ?string $href = null;

    /**
     * @var string|null
     */
    protected ?string $href_target = null;

    /**
     * Cell constructor.
     *
     * @param Column $column
     * @param object $result
     */
    public function __construct(Column $column, object $result)
    {
        $this->setColumn($column);
        $this->setValue($column->formattedValue($result));
        $this->setRawValue($column->value($result));
        $this->setTooltip($column->generateLink($result, 'tooltip'));
        $this->setHref($column->generateLink($result));
        $this->setHrefTarget($column->hrefTarget());
    }

    /**
     * @return Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }

    /**
     * @param Column $column
     *
     * @return Cell
     */
    public function setColumn(Column $column): self
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     *
     * @return Cell
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRawValue(): ?string
    {
        return $this->raw_value;
    }

    /**
     * @param string|null $raw_value
     *
     * @return Cell
     */
    public function setRawValue(?string $raw_value): self
    {
        $this->raw_value = $raw_value;

        return $this;
    }

    /**
     * @param object|null $result
     *
     * @return string|null
     */
    public function getFormattedValue(?object $result): ?string
    {
        $value = $this->getValue();

        if (is_null($value)) {
            return null;
        }

        return $this->getType()->format($value, $result);
    }

    /**
     * @return BaseType|null
     */
    public function getType(): ?BaseType
    {
        return optional($this->column)->type();
    }

    /**
     * @return null|string
     */
    public function getHref(): ?string
    {
        return $this->href;
    }

    /**
     * @param $href
     *
     * @return $this
     */
    public function setHref($href): self
    {
        $this->href = $href;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }

    /**
     * @param $tooltip
     *
     * @return Cell
     */
    public function setTooltip($tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHrefTarget(): ?string
    {
        return $this->href_target;
    }

    /**
     * @param string $target
     *
     * @return Cell
     */
    public function setHrefTarget(?string $target): self
    {
        $this->href_target = $target;

        return $this;
    }

    /**
     * @throws Throwable
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue() ?? '';
    }

    /**
     * Get the instance as an array.
     *
     * @param object|null $result
     *
     * @throws Throwable
     *
     * @return array
     */
    public function toArrayWithResult(?object $result): array
    {
        return [
            'column' => [
                'name' => $this->column->name(),
                'label' => $this->column->label(),
                'type' => $this->column->type()->name(),
            ],
            'value_raw' => $this->getValue(),
            'value_formatted' => $this->getFormattedValue($result),
            'tooltip' => $this->getTooltip(),
            'href' => $this->getHref(),
            'href_target' => $this->getHrefTarget(),
        ];
    }
}
