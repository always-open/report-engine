<?php

namespace AlwaysOpen\ReportEngine\Tests\Unit;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Cell;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Column;
use AlwaysOpen\ReportEngine\BaseFeatures\Data\Types\Text;
use AlwaysOpen\ReportEngine\Tests\TestCase;

class CellTest extends TestCase
{
    protected $column;

    protected $result;

    protected $cell;

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
    public function cell_outputs_correct_value()
    {
        $this->assertEquals('Test', $this->cell->getValue());
    }

    /** @test */
    public function cell_outputs_correct_column()
    {
        $this->assertEquals($this->column, $this->cell->getColumn());
    }

    /** @test */
    public function cell_outputs_correct_to_array()
    {
        $array = $this->cell->toArrayWithResult($this->result);

        $this->assertIsArray($array);
        $this->assertNotEmpty($array);
        $this->assertIsArray($array['column']);
        $this->assertEquals('Test', $array['value_raw']);
        $this->assertNull($array['tooltip']);
        $this->assertNull($array['href']);
        $this->assertNull($array['href_target']);
    }
}
