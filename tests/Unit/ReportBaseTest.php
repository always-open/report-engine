<?php

namespace BluefynInternational\ReportEngine\Tests\Unit;

use BluefynInternational\ReportEngine\Tests\TestCase;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class ReportBaseTest extends TestCase
{
    /**
     * @var Request|null
     */
    protected ?Request $fakeRequest = null;

    /**
     * @var DummyReport|null
     */
    protected ?DummyReport $dummyReport = null;

    public function setUp(): void
    {
        $this->fakeRequest = Request::create('/fakeTest.json?_format=json', 'GET', ['name' => 'Bob', '_format' => 'json']);

        $this->dummyReport = new DummyReport($this->fakeRequest);

        parent::setUp();
    }

    /** @test */
    public function basicReportAttributes()
    {
        $this->assertNotEmpty($this->dummyReport->title());
        $this->assertNotEmpty($this->dummyReport->description());
        $this->assertNotEmpty($this->dummyReport->availableColumns());
        $this->assertCount(1, $this->dummyReport->availableColumns());
        $this->assertInstanceOf(Builder::class, $this->dummyReport->baseQuery());
    }
}
