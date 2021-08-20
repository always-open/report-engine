<?php

namespace BluefynInternational\ReportEngine;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Cell;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Column;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Row;
use BluefynInternational\ReportEngine\BaseFeatures\ReportButton;
use BluefynInternational\ReportEngine\BaseFeatures\Traits\ColumnsMappable;
use BluefynInternational\ReportEngine\BaseFeatures\Traits\Filterable;
use BluefynInternational\Sidekick\Helpers\Query;
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
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

abstract class ReportBase implements Responsable, Arrayable
{
    use ColumnsMappable;
    use Filterable;

    /**
     * @var Builder
     */
    protected $query;

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

    /**
     * @var Request
     */
    protected $currentRequest;

    /**
     * @var Collection
     */
    protected $results;

    /**
     * @var Collection
     */
    protected $columns;

    /**
     * @var ?string
     */
    protected $emptyMessage = null;

    /**
     * @var array
     */
    protected $rowsArray = [];

    /**
     * @var array
     */
    protected $reportButtons = [];

    /**
     * @var bool
     */
    protected $autoloadInitialData = false;

    /**
     * ReportBase constructor.
     *
     * @param Request $currentRequest
     */
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

    /**
     * @return string
     */
    public function slug(): string
    {
        return Str::slug($this->title());
    }

    /**
     * @return string
     */
    public function emptyMessage() : string
    {
        return $this->emptyMessage ?? 'No Data Found';
    }

    /**
     * @return Request
     */
    public function getCurrentRequest() : Request
    {
        return $this->currentRequest;
    }

    /**
     * Build the report.
     */
    public function build(): void
    {
        $this->buildColumns()
            ->initFeatures()
            ->fetchData()
            ->buildRows();
    }

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
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

    /**
     * @return Collection
     */
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

    /**
     * @return Collection
     */
    public function generateTabulatorColumns() : Collection
    {
        return $this->buildColumns()->columns->map(function (Column $column) {
            $array = [
                'title' => $column->label(),
                'field' => $column->name(),
                'formatter' => $column->formatter(),
            ];

            if ($column->shouldSum()) {
                $array['bottomCalc'] = $column->bottomCalc() ?? 'sum';
                if ($column->topCalc()) {
                    $array['topCalc'] = $column->topCalc();
                }
            }

            if ($column->hidden()) {
                $array['visible'] = false;
            }

            if ($column->type()->formatterParams()) {
                $array['formatterParams'] = $column->type()->formatterParams();
            }

            return $array;
        });
    }

    /**
     * @return Collection
     */
    protected function getColumns() : Collection
    {
        if ($this->columns->isNotEmpty()) {
            return $this->columns;
        }

        return $this->buildColumns()->columns;
    }

    /**
     * @return Collection
     */
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

    /**
     * @return string
     */
    protected function getSql(): string
    {
        $query = $this->buildColumns()
            ->initFeatures()
            ->getQueryWithFeaturesApplied();

        return Query::toString($query);
    }

    /**
     * @return $this
     */
    protected function buildColumns() : self
    {
        foreach ($this->availableColumns() as $name => $config) {
            if (is_array($config)) {
                $column = new Column($name, $config);
            } elseif ($config instanceof Column) {
                $column = $config;
            } else {
                throw new \InvalidArgumentException('Column config must be an instance of array or ' . Column::class);
            }

            $this->columns->push($column);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function initFeatures() : self
    {
        if (method_exists($this, 'initSorting')) {
            $this->initSorting();
        }

        return $this;
    }

    /**
     * @return Builder
     */
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
        return response()->json($this->getConfig());
    }

    public function getConfig() : array
    {
        return [
            'title' => $this->title(),
            'emptyMessage' => $this->emptyMessage(),
            'columns' => $this->generateTabulatorColumns(),
            'filterColumns' => $this->getFilterableColumns(),
            'autoloadInitialData' => $this->autoloadInitialData,
            'route' => route($this->getCurrentRequest()->route()->getName(), $this->getCurrentRequest()->route()->parameters()),
            'rowContextActions' => $this->rowContextActions(),
            'reportButtons' => $this->reportButtons(),
        ];
    }

    /**
     * @return JsonResponse
     */
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
     * @return Response
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

    /**
     * @return JsonResponse
     */
    public function toReport(): JsonResponse
    {
        return response()->json($this->toArray());
    }

    /**
     * @return Response
     */
    public function toHtml() : Response
    {
        return response()->view('report-engine::base-web', $this->getConfig());
    }

    /**
     * @return null|array
     */
    public function rowContextActions() : ?array
    {
        return null;
    }

    /**
     * @return array
     */
    public function reportButtons(): array
    {
        return $this->reportButtons;
    }

    /**
     * @param ReportButton $button
     *
     * @return $this
     */
    public function addReportButton(ReportButton $button) : self
    {
        $this->reportButtons[] = $button;

        return $this;
    }

    /**
     * @param Collection $filterColumns
     *
     * @throws \Throwable
     *
     * @return string
     */
    public static function rendersFilters(Collection $filterColumns) : string
    {
        $output = '';

        $fullFilterRows = $filterColumns->count() % 4 === 0;

        $filterColumns->chunk(4)->each(function (Collection $columns) use (&$output, $fullFilterRows) {
            $output .= view('report-engine::partials.filters-row')->with([
                'columns' => $columns,
                'offset' => 3 - count($columns),
                'includeSubmit' => ! $fullFilterRows && count($columns) < 4,
            ])->render();
        });

        if (! empty($output) && $fullFilterRows) {
            $output .= view('report-engine::partials.filters-row')
                ->with(['offset' => 3])
                ->render();
        }

        return $output;
    }
}
