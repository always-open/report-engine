<?php

namespace BluefynInternational\ReportEngine\Tests\Unit;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Column;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\ContainsFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotContainFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsEmptyFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsFalseFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsNotEmptyFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\IsTrueFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use BluefynInternational\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;
use BluefynInternational\ReportEngine\Tests\TestCase;
use BluefynInternational\Sidekick\Helpers\Query;
use Illuminate\Support\Facades\DB;

class FilterTest extends TestCase
{
    protected ?Column $column = null;

    public function setUp(): void
    {
        $this->column = new Column('dummy', []);

        parent::setUp();
    }

    /** @test */
    public function containsFilter()
    {
        $filter = new ContainsFilter($this->column);
        $filter->setValue('find me');
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('%find me%', Query::toString($query));
    }

    /** @test */
    public function doesNotContainFilter()
    {
        $filter = new DoesNotContainFilter($this->column);
        $filter->setValue('find me');
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('%find me%', Query::toString($query));
        $this->assertStringContainsString('not like', Query::toString($query));
    }

    /** @test */
    public function doesNotEqualFilter()
    {
        $filter = new DoesNotEqualFilter($this->column);
        $filter->setValue('find me');
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString("!= 'find me'", Query::toString($query));
    }

    /** @test */
    public function greaterThanFilter()
    {
        $filter = new GreaterThanFilter($this->column);
        $filter->setValue(9);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('> 9', Query::toString($query));
    }

    /** @test */
    public function greaterThanOrEqualToFilter()
    {
        $filter = new GreaterThanOrEqualFilter($this->column);
        $filter->setValue(9);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('>= 9', Query::toString($query));
    }

    /** @test */
    public function lessThanFilter()
    {
        $filter = new LessThanFilter($this->column);
        $filter->setValue(9);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('< 9', Query::toString($query));
    }

    /** @test */
    public function lessThanOrEqualToFilter()
    {
        $filter = new LessThanOrEqualFilter($this->column);
        $filter->setValue(9);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('<= 9', Query::toString($query));
    }

    /** @test */
    public function isEmptyFilter()
    {
        $filter = new IsEmptyFilter($this->column);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('`dummy` is null', Query::toString($query));
    }

    /** @test */
    public function isNotEmptyFilter()
    {
        $filter = new IsNotEmptyFilter($this->column);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('`dummy` is not null', Query::toString($query));
    }

    /** @test */
    public function isFalseFilter()
    {
        $filter = new IsFalseFilter($this->column);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('`dummy` = 0', Query::toString($query));
    }

    /** @test */
    public function isTrueFilter()
    {
        $filter = new IsTrueFilter($this->column);
        $query = $filter->apply(DB::table('fake'));
        $this->assertStringContainsString('`dummy` = 1', Query::toString($query));
    }
}
