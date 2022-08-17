<?php

namespace AlwaysOpen\ReportEngine;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Cell;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Column;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Row;
use AlwaysOpen\ReportEngine\BaseFeatures\ReportButton;
use AlwaysOpen\ReportEngine\BaseFeatures\Traits\ColumnsMappable;
use AlwaysOpen\ReportEngine\BaseFeatures\Traits\Filterable;
use AlwaysOpen\Sidekick\Helpers\Query;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

abstract class ReportBase implements Responsable, Arrayable
{
    use ColumnsMappable;
    use Filterable;

    /**
     * @var string
     */
    protected const SPECIAL_SEPARATOR = '∞|∞';

    /**
     * @var string
     */
    public const JSON_FORMAT = 'json';

    /**
     * @var string
     */
    public const HTML_FORMAT = 'html';

    public const LAYOUT_FIT_DATA = 'fitData';
    public const LAYOUT_FIT_DATA_FILL = 'fitDataFill';
    public const LAYOUT_FIT_DATA_STRETCH = 'fitDataStretch';
    public const LAYOUT_FIT_DATA_TABLE = 'fitDataTable';
    public const LAYOUT_FIT_COLUMNS = 'fitColumns';
    public const LAYOUT_OPTIONS = [
        self::LAYOUT_FIT_DATA,
        self::LAYOUT_FIT_DATA_FILL,
        self::LAYOUT_FIT_DATA_STRETCH,
        self::LAYOUT_FIT_DATA_TABLE,
        self::LAYOUT_FIT_COLUMNS,
    ];

    protected Builder $query;

    protected Request $currentRequest;

    protected Collection $results;

    protected Collection $columns;

    protected ?string $emptyMessage = null;

    protected array $rowsArray = [];

    protected array $reportButtons = [];

    protected bool $autoloadInitialData = false;

    /**
     * Whether the rows should be selectable
     */
    protected bool $selectable = false;

    protected bool $movableColumns = false;

    protected bool $tooltips = false;

    protected bool $layoutColumnsOnNewData = false;

    protected string $layout = self::LAYOUT_FIT_COLUMNS;

    public function __construct(Request $currentRequest)
    {
        ini_set('memory_limit', '3G');

        $this->currentRequest = $currentRequest;
        $this->columns = collect();
        $this->results = collect();
    }

    abstract public function title(): string;

    abstract public function description(): string;

    abstract public function baseQuery() : Builder;

    abstract public function availableColumns(): array;

    public function slug(): string
    {
        return Str::slug($this->title());
    }

    public function emptyMessage() : string
    {
        return $this->emptyMessage ?? 'No Data Found';
    }

    public function getCurrentRequest() : Request
    {
        return $this->currentRequest;
    }

    public function build() : self
    {
        return $this->buildColumns()
            ->initFeatures()
            ->fetchData()
            ->buildRows();
    }

    public function fetchData() : self
    {
        $this->query = $this->getQueryWithFeaturesApplied();

        $this->results = $this->runQuery($this->query);

        return $this;
    }

    /**
     * @param Builder  $query
     * @param int|null $perPageLimit
     *
     * @TODO Manage pagination
     *
     * @return Collection
     */
    protected function runQuery(Builder $query, ?int $perPageLimit = null): Collection
    {
        return $query->get();
    }

    protected function buildRows() : self
    {
        $this->results->each(function (object $result) {
            $row = new Row();
            $row->setResultItems($result);
            /** @var Column $column */
            foreach ($this->columns as $column) {
                $cell = new Cell($column, $result);
                $row->appendCell($cell);
            }
            $this->rowsArray[] = $row->toArray();
        });

        return $this;
    }

    protected function generateTabulatorData() : Collection
    {
        $data = collect();
        $this->results->each(function (object $result) use (&$data) {
            $row = collect();
            /** @var Column $column */
            foreach ($this->columns as $column) {
                $cell = new Cell($column, $result);
                $row->put($column->name(), $cell->getFormattedValue($result));
                if ($column->includeRaw()) {
                    $row->put($column->name() . '_raw_value', $cell->getRawValue());
                }
            }
            $data->push($row);
        });

        return $data;
    }

    public function generateTabulatorColumns() : Collection
    {
        return $this->buildColumns()->columns->map(function (Column $column) {
            $array = [
                'title' => $column->label(),
                'field' => $column->name(),
                'formatter' => $column->formatter(),
            ];

            if ($column->shouldSum()) {
                if ($column->bottomCalc()) {
                    $array['bottomCalc'] = $column->bottomCalc();
                    if ($column->bottomCalcParams()) {
                        $array['bottomCalcParams'] = $column->bottomCalcParams();
                    }
                }
                if ($column->topCalc()) {
                    $array['topCalc'] = $column->topCalc();
                    if ($column->topCalcParams()) {
                        $array['topCalcParams'] = $column->topCalcParams();
                    }
                }
            }

            foreach ($column->optionalConfigFields() as $config_name => $function_name) {
                $value = null;
                foreach (explode('.', $function_name) as $function) {
                    $value = ($value ?? $column)->{$function}();
                }

                if ($value) {
                    $array[$config_name] = $value;
                }
            }

            if ($column->hidden()) {
                $array['visible'] = false;
            }

            return $array;
        });
    }

    protected function getColumns() : Collection
    {
        if ($this->columns->isNotEmpty()) {
            return $this->columns;
        }

        return $this->buildColumns()->columns;
    }

    protected function getFilterableColumns() : Collection
    {
        $filteredBy = $this->getCurrentlyFilteredBy($this->getFilterParams());

        return $this->getColumns()
            ->filter(function (Column $column) {
                return $column->isFilterable();
            })
            ->map(function (Column $column) use ($filteredBy) {
                $column->setFilterValue($filteredBy->get($column->aliasOrName()));

                return $column;
            });
    }

    protected function getSql(): string
    {
        $query = $this->buildColumns()
            ->initFeatures()
            ->getQueryWithFeaturesApplied();

        return Query::toString($query);
    }

    protected function buildColumns() : self
    {
        foreach ($this->availableColumns() as $name => $config) {
            if (is_array($config)) {
                $column = new Column($name, $config);
            } elseif ($config instanceof Column) {
                $column = $config;
            } else {
                throw new InvalidArgumentException('Column config must be an instance of array or ' . Column::class);
            }

            $this->columns->push($column);
        }

        return $this;
    }

    protected function initFeatures() : self
    {
        if (method_exists($this, 'initSorting')) {
            $this->initSorting();
        }

        return $this;
    }

    protected function getQueryWithFeaturesApplied() : Builder
    {
        $query = $this->baseQuery();

        if (method_exists($this, 'applySorting')) {
            $this->query = $this->applySorting($query);
        }

        if (method_exists($this, 'applyFilters')) {
            $this->query = $this->applyFilters($this->getFilterParams(), $query);
        }

        return $query;
    }

    /**
     * @return mixed
     */
    protected function getFilterParams()
    {
        return $this->getCurrentRequest()->get('filters', []);
    }

    /**
     * Generate data array used for API / Javascript.
     *
     * @return array
     */
    public function toArray(): array
    {
        $this->build();

        return [
            'columns' => collect($this->columns)->map(function (Column $column) {
                return $column->toArray();
            }),
            'data' => $this->rowsArray,
            'emptyMessage' => $this->emptyMessage(),
        ];
    }

    public function toConfig() : JsonResponse
    {
        return response()->json($this->getConfig(true));
    }

    public function getConfig(bool $asArray = false) : array
    {
        $reportButtons = $this->reportButtons();

        if ($asArray) {
            $reportButtons = collect($reportButtons)->toArray();
        }

        return [
            'title' => $this->title(),
            'emptyMessage' => $this->emptyMessage(),
            'columns' => $this->generateTabulatorColumns(),
            'filterColumns' => $this->getFilterableColumns(),
            'autoloadInitialData' => $this->autoloadInitialData,
            'route' => $this->getRoute(),
            'rowContextActions' => $this->rowContextActionsForConfig($asArray),
            'reportButtons' => $reportButtons,
            'selectable' => $this->selectable,
            'movableColumns' => $this->movableColumns,
            'tooltips' => $this->tooltips,
            'layoutColumnsOnNewData' => $this->layoutColumnsOnNewData,
            'layout' => $this->layout,
        ];
    }

    public function getRoute() : string
    {
        return route($this->getCurrentRequest()->route()->getName(), $this->getCurrentRequest()->route()->parameters());
    }

    public function toJson(): JsonResponse
    {
        $data = $this->buildColumns()
            ->initFeatures()
            ->fetchData()
            ->generateTabulatorData();

        return response()->json($data);
    }

    /**
     * Return the unbinded SQL Generated by the report via .sql endpoint.
     *
     * @return Application|ResponseFactory|Response
     */
    public function toSql()
    {
        return response($this->getSql())
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Run MySQL EXPLAIN on the report's underlying query.
     *
     * @return Application|ResponseFactory|Response
     */
    public function toExplain()
    {
        /** @var Collection $response */
        $response = collect(DB::select("EXPLAIN " . $this->getSql()))->toJson(JSON_PRETTY_PRINT);

        return response()
            ->setContent($response)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * @param Request $request
     *
     * @return SymfonyResponse|Response
     */
    public function toResponse($request) : SymfonyResponse
    {
        $format = $this->getCurrentRequest()->route('_format') ?? self::HTML_FORMAT;

        $method = Str::camel('to_' . strtolower($format));

        if (! method_exists($this, $method)) {
            abort(SymfonyResponse::HTTP_NOT_ACCEPTABLE, 'No valid response could be generated for type: ' . $format);
        }

        return $this->{$method}();
    }

    public function toReport(): JsonResponse
    {
        return response()->json($this->toArray());
    }

    public function toHtml() : Response
    {
        return response()->view('report-engine::base-web', $this->getConfig());
    }

    public function rowContextActionsForConfig(bool $asArray = false) : ?array
    {
        if ($asArray) {
            return collect($this->rowContextActions())->toArray();
        }

        return $this->rowContextActions();
    }

    public function rowContextActions() : ?array
    {
        return null;
    }

    public function reportButtons(): array
    {
        return $this->reportButtons;
    }

    public function addReportButton(ReportButton $button) : self
    {
        $this->reportButtons[] = $button;

        return $this;
    }

    /**
     * @param Collection $filterColumns
     * @param int        $columnsWide
     *
     * @throws Throwable
     *
     * @return string
     */
    public static function rendersFilters(Collection $filterColumns, int $columnsWide = 4) : string
    {
        $output = '';

        $fullFilterRows = $filterColumns->count() % $columnsWide === 0;

        $filterColumns->chunk($columnsWide)->each(function (Collection $columns) use (&$output, $fullFilterRows, $columnsWide) {
            $output .= view('report-engine::partials.filters-row')->with([
                'columns' => $columns,
                'offset' => ($columnsWide - 1) - count($columns),
                'includeSubmit' => ! $fullFilterRows && count($columns) < $columnsWide,
            ])->render();
        });

        if (! empty($output) && $fullFilterRows) {
            $output .= view('report-engine::partials.filters-row')
                ->with(['offset' => $columnsWide - 1])
                ->render();
        }

        return $output;
    }

    public function getMetaData() : array
    {
        $encodedData = $this->currentRequest->get('metaData', '');

        return json_decode(json: base64_decode($encodedData), associative: true) ?? [];
    }
}
