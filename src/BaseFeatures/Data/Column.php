<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Bases\BaseType;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Enum;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Text;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\BaseFilter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Column implements Arrayable
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array|null
     */
    protected ?array $config = null;

    /**
     * @var Collection|null
     */
    protected ?Collection $filterValue;

    const ACTION_WHERE = 'where';

    const ACTION_HAVING = 'having';

    /**
     * Column constructor.
     *
     * @param string     $name
     * @param array|null $config
     */
    public function __construct(string $name, ?array $config)
    {
        $this->name = $name;
        $this->initializeConfig($config);
    }

    /**
     * @param array|null $config
     */
    public function initializeConfig(?array $config): void
    {
        foreach ($config as $key => $val) {
            $method = 'set' . Str::studly($key);

            if (method_exists($this, $method)) {
                $this->{$method}($val);
            } else {
                Arr::set($this->config, $key, $val);
            }
        }
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        $this->config['value'] = $value;

        return $this;
    }

    /**
     * @param object $result
     *
     * @return null|string
     */
    public function value($result): ?string
    {
        if (isset($this->config['value'])) {
            if (is_callable($this->config['value'])) {
                $value = $this->config['value']($result);
            } else {
                $value = (string) $this->config['value'];
            }
        } else {
            $value = data_get($result, $this->name());
        }

        return $value;
    }

    public function setValueFormatter(?\Closure $valueFormatter): self
    {
        $this->config['value_formatter'] = $valueFormatter;

        return $this;
    }

    /**
     * @param $result
     *
     * @return string|null
     */
    public function formattedValue($result)
    {
        $value = $this->value($result);
        if (! empty($this->config['value_formatter'])) {
            $value = $this->config['value_formatter']($result);
        }

        return $value;
    }

    /**
     * @return BaseType
     */
    public function type(): BaseType
    {
        if (! empty($this->config['type'])) {
            if ($this->config['type'] instanceof BaseType) {
                return $this->config['type'];
            }
        }

        return new Text();
    }

    /**
     * @param BaseType $type
     *
     * @return $this
     */
    public function setType(BaseType $type): self
    {
        $this->config['type'] = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMinWidth() : ?string
    {
        return $this->config['minWidth'] ?? null;
    }

    /**
     * @param string $minWidth
     *
     * @return $this
     */
    public function minWidth(string $minWidth): self
    {
        $this->config['minWidth'] = $minWidth;

        return $this;
    }

    /**
     * @return bool
     */
    public function hidden() : bool
    {
        return $this->config['hidden'] ?? false;
    }

    /**
     * @param bool $hidden
     *
     * @return $this
     */
    public function setHidden(bool $hidden): self
    {
        $this->config['hidden'] = $hidden;

        return $this;
    }

    public function setShouldSum(bool $shouldSum): self
    {
        $this->config['should_sum'] = $shouldSum;

        return $this;
    }

    public function shouldSum() : bool
    {
        return $this->config['should_sum'] ?? false;
    }

    public function setIncludeRaw(bool $includeRaw): self
    {
        $this->config['include_raw'] = $includeRaw;

        return $this;
    }

    public function includeRaw() : bool
    {
        return $this->config['include_raw'] ?? false;
    }

    /**
     * @return string
     */
    public function action() : string
    {
        return $this->config['action'] ?? self::ACTION_WHERE;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction(string $action): self
    {
        $this->config['action'] = $action;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->config['label'] = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return $this->config['label'] ?? $this->name();
    }

    /**
     * @return string
     */
    public function tooltip(): string
    {
        $output = data_get($this->config, 'tooltip');

        return is_string($output) ? $output : $this->label();
    }

    /**
     * @return bool
     */
    public function truncatable(): bool
    {
        return (bool) data_get($this->config, 'truncatable', true);
    }

    /**
     * @param bool $sortable
     *
     * @return $this
     */
    public function setSortable(bool $sortable): self
    {
        $this->config['sortable'] = $sortable;

        return $this;
    }

    /**
     * @return bool
     */
    public function sortable(): bool
    {
        return (bool) Arr::get($this->config, 'sortable', true);
    }

    /**
     * @return null|string|Expression
     */
    public function aliasFrom()
    {
        return Arr::get($this->config, 'alias_from');
    }

    /**
     * @param string|Expression $column
     *
     * @return \BluefynInternational\ReportEngine\BaseFeatures\Data\Column
     */
    public function setAliasFrom($column): self
    {
        $this->config['alias_from'] = $column;

        return $this;
    }

    public function topCalc() : ?string
    {
        return Arr::get($this->config, 'top_calc');
    }

    public function setTopCalc(?string $topCalcFunction): self
    {
        $this->config['top_calc'] = $topCalcFunction;

        return $this;
    }

    /**
     * @return mixed
     */
    public function topCalcParams()
    {
        return Arr::get($this->config, 'top_calc_params');
    }

    /**
     * @param mixed $topCalcParams
     *
     * @return $this
     */
    public function setTopCalcParams($topCalcParams): self
    {
        $this->config['top_calc_params'] = $topCalcParams;

        return $this;
    }

    public function bottomCalc() : ?string
    {
        return Arr::get($this->config, 'bottom_calc');
    }

    public function setBottomCalc(?string $bottomCalcFunction): self
    {
        $this->config['bottom_calc'] = $bottomCalcFunction;

        return $this;
    }

    public function downloadAccessorFunction() : ?string
    {
        return Arr::get($this->config, 'download_access_function');
    }

    public function setDownloadAccessorFunction(?string $downloadAccessFunction): self
    {
        $this->config['download_access_function'] = $downloadAccessFunction;

        return $this;
    }

    /**
     * @return mixed
     */
    public function bottomCalcParams()
    {
        return Arr::get($this->config, 'bottom_calc_params');
    }

    /**
     * @param mixed $bottomCalcParams
     *
     * @return $this
     */
    public function setBottomCalcParams($bottomCalcParams): self
    {
        $this->config['bottom_calc_params'] = $bottomCalcParams;

        return $this;
    }

    /**
     * @return string|Expression
     */
    public function aliasOrName()
    {
        $alias_or_name = $this->aliasFrom() ?? $this->name();

        if ($alias_or_name instanceof Expression) {
            return $alias_or_name;
        }

        return (string) $alias_or_name;
    }

    /**
     * @return null|string
     */
    public function aliasSort(): ?string
    {
        return Arr::get($this->config, 'alias_sort');
    }

    /**
     * @param string $column
     *
     * @return \BluefynInternational\ReportEngine\BaseFeatures\Data\Column
     */
    public function setAliasSort(string $column): self
    {
        $this->config['alias_sort'] = $column;

        return $this;
    }

    /**
     * @return string|Expression
     */
    public function aliasSortOrName()
    {
        return $this->aliasSort() ?? $this->name();
    }

    /**
     * @param string $key
     */
    public function forgetConfig(string $key): void
    {
        Arr::forget($this->config, $key);
    }

    /**
     * @param mixed  $result
     * @param string $item
     *
     * @return string|null
     */
    public function generateLink($result, string $item = 'href'): ?string
    {
        if (empty($this->config[$item])) {
            return null;
        }

        return is_callable($this->config[$item])
                        ? $this->config[$item]($result)
                        : $this->config[$item];
    }

    /**
     * Get the href target property if it exists.
     *
     * @return null|string
     */
    public function hrefTarget(): ?string
    {
        return Arr::get($this->config, 'href_target');
    }

    /**
     * Get the available filters for this column.
     *
     * @return array
     */
    public function filters(): array
    {
        if (! $this->isFilterable()) {
            return [];
        }

        return $this->type()::availableFilters();
    }

    /**
     * @return array
     */
    public function filterInstances() : array
    {
        $filterInstances = [];

        $filters = $this->filters();

        foreach ($filters as $filter) {
            $filterInstances[] = new $filter($this);
        }

        return $filterInstances;
    }

    /**
     * @param bool $value
     *
     * @return \BluefynInternational\ReportEngine\BaseFeatures\Data\Column
     */
    public function setIsFilterable(bool $value): self
    {
        $this->config['filterable'] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFilterable(): bool
    {
        return (bool) Arr::get($this->config, 'filterable', false);
    }

    /**
     * @return array|null
     */
    public function options(): ?array
    {
        if ($this->type() instanceof Enum) {
            return $this->type()->getOptions();
        }

        return null;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        $action_types = collect($this->filterInstances())
            ->map(function (BaseFilter $filter) {
                /**
                 * @var BaseFilter $filter
                 */
                return $filter->toArray();
            });

        return [
            'title' => $this->label(),
            'field' => $this->name(),
            'filter_type' => $this->type()
                ->getConfig(
                    $this->label(),
                    $this->name(),
                    $action_types->toArray(),
                    $this->type(),
                    $this->getFilterValue(),
                ),
        ];
    }

    public function setFilterValue(?Collection $value)
    {
        $this->filterValue = $value;
    }

    public function getFilterValue() : Collection
    {
        return $this->filterValue ?? collect();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderFilter()
    {
        return $this->type()
            ->renderFilter($this->label(), $this->name(), $this->filterInstances(), $this->type(), $this->getFilterValue());
    }

    /**
     * @return string
     */
    public function formatter() : string
    {
        return $this->type()->formatter();
    }
}
