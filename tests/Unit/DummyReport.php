<?php

namespace BluefynInternational\ReportEngine\Tests\Unit;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Text;
use BluefynInternational\ReportEngine\ReportBase;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DummyReport extends ReportBase
{
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Dummy Report';
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return 'Dummy report for testing';
    }

    /**
     * @return Builder
     */
    public function baseQuery(): Builder
    {
        return DB::table('fake')->select([
            'id',
            'name',
        ]);
    }

    /**
     * @return array
     */
    public function availableColumns(): array
    {
        return [
            'name' => [
                'type' => new Text(),
                'filterable' => true,
            ],
        ];
    }
}
