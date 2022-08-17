<?php

namespace AlwaysOpen\ReportEngine\Tests\Unit;

use AlwaysOpen\ReportEngine\BaseFeatures\Data\Column;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\ContainsFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotContainFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\DoesNotEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\GreaterThanOrEqualFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsEmptyFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsFalseFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsNotEmptyFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\IsTrueFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanFilter;
use AlwaysOpen\ReportEngine\BaseFeatures\Filters\LessThanOrEqualFilter;
use AlwaysOpen\ReportEngine\Tests\TestCase;
use AlwaysOpen\Sidekick\Helpers\Query;
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
