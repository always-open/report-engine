<?php

namespace AlwaysOpen\ReportEngine\BaseFeatures\Data\Types;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Support\Arr;

class Dollar extends BaseType
{
    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * Dollar constructor.
     *
     * @param array|null $options
     */
    public function __construct(?array $options = null)
    {
        $this->options = $options;
    }

    /**
     * @param object|null $result
     *
     * @return string
     */
    public function getCurrency(?object $result): string
    {
        $currency = null;
        // Look for the currency in the results.
        if ($this->getCurrencyColumn()) {
            $currency = $result->{$this->getCurrencyColumn()} ?? null;
        }

        return $currency ?? 'USD';
    }

    /**
     * @return string|null
     */
    public function getCurrencyColumn(): ?string
    {
        return Arr::get($this->options, 'currency_column');
    }

    /**
     * @param string|float|int|mixed $value
     * @param object|null            $result
     *
     * @return string
     *
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function typeFormat($value, ?object $result = null): string
    {
        return Money::of($value, $this->getCurrency($result), null, RoundingMode::HALF_DOWN)
            ->formatTo('us_en');
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

    public function inputType() : string
    {
        return 'number';
    }
}
