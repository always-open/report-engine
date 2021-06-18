<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data\Types;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\EqualsFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;
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
    public function format($value, ?object $result = null): string
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
}
