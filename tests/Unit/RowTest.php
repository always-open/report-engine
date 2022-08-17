<?php

namespace AlwaysOpen\ReportEngine\Tests\Unit;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Cell;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Column;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Row;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Text;
use AlwaysOpen\ReportEngine\Tests\TestCase;

class RowTest extends TestCase
{
    protected ?Column $column = null;

    protected $result;

    protected ?Cell $cell = null;

    public function setUp(): void
    {
        $this->result = (object) [
            'id' => 1,
            'name' => 'Test',
            'dummy' => 'Tester',
        ];
        $this->column = new Column('name', [
            'type' => new Text(),
            'filterable' => true,
        ]);
        $this->cell = new Cell($this->column, $this->result);

        parent::setUp();
    }

    /** @test */
    public function row_contains_cells()
    {
        $row = new Row();
        $row->setResultItems($this->result);
        $row->appendCell($this->cell);

        $this->assertCount(1, $row->getCells());
        $this->assertEquals($this->result, $row->getResultItems());
        $this->assertNotEmpty($row->toArray()['cells']);
    }
}
