<?php

namespace AlwaysOpen\ReportEngine\Tests\Unit;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Text;
use AlwaysOpen\ReportEngine\ReportBase;
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
