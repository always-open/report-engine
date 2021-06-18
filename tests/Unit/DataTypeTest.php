<?php

namespace BluefynInternational\ReportEngine\Tests\Unit;

use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\DateTime;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Decimal;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Dollar;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Integer;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\NullableDecimal;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\NullableInteger;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Number;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Percentage;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Text;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\Url;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\YesNo;
use BluefynInternational\ReportEngine\BaseFeatures\Data\Types\YesNoShort;
use BluefynInternational\ReportEngine\Tests\TestCase;

class DataTypeTest extends TestCase
{
    /** @test */
    public function dateTime()
    {
        $type = new DateTime();

        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $type->format(now()));

        $type = new DateTime('Y/m/d');
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $type->format(now()));
    }

    /** @test */
    public function decimal()
    {
        $type = new Decimal();
        $this->assertEquals(9.99, $type->format('9.991'));
        $this->assertEquals(1.23, $type->format('1.234'));
        $this->assertEquals(1.23, $type->format(1.2345));
        $this->assertEquals(1.20, $type->format('1.20'));
        $type = new Decimal(5);
        $this->assertEquals(1.23456, $type->format('1.23456'));
    }

    /** @test */
    public function dollar()
    {
        $type = new Dollar();
        // Some environments will add a space between the amount and the $ while others won't
        $this->assertStringStartsWith('$', $type->format('127.7'));
        $this->assertStringEndsWith('127.70', $type->format('127.7'));
    }

    /** @test */
    public function integer()
    {
        $type = new Integer();
        $this->assertEquals(9, $type->format('9.99'));
        $this->assertEquals(1, $type->format('1.23'));
        $this->assertEquals(1234567, $type->format('1234567.7'));
        $this->assertEquals(0, $type->format(null));
    }

    /** @test */
    public function nullableInteger()
    {
        $type = new NullableInteger(null);
        $this->assertEquals(9, $type->format('9.99'));
        $this->assertEquals(1, $type->format('1.23'));
        $this->assertEquals(1234567, $type->format('1234567.7'));
        $this->assertEquals('--', $type->format(null));
        $type = new NullableInteger('N/A');
        $this->assertEquals('--', $type->format('N/A'));
    }

    /** @test */
    public function nullableDecimal()
    {
        $type = new NullableDecimal(null);
        $this->assertEquals(9.99, $type->format('9.99'));
        $this->assertEquals(1.23, $type->format('1.23'));
        $this->assertEquals(1234567.70, $type->format('1234567.7'));
        $this->assertEquals('--', $type->format(null));
        $type = new NullableInteger('N/A');
        $this->assertEquals('--', $type->format('N/A'));
    }

    /** @test */
    public function number()
    {
        $type = new Number();
        $this->assertEquals('9.99', $type->format(9.99));
        $this->assertEquals('12345', $type->format('12345'));

        $type->enableSeparator();
        $this->assertEquals('12,345', $type->format('12345'));
        $type->setSeparator('*');
        $this->assertEquals('12*345', $type->format('12345'));
        $this->assertEquals('12*346', $type->format('12345.99'));
        $type->setSeparator('.');
        $this->assertEquals('12.346', $type->format('12345.99'));
    }

    /** @test */
    public function percentage()
    {
        $type = new Percentage();
        $this->assertEquals('9.9%', $type->format('9.913'));
        $this->assertEquals('100.0%', $type->format('100.03'));
        $this->assertEquals('-10.0%', $type->format('-10'));
    }

    /** @test */
    public function text()
    {
        $type = new Text();
        $this->assertEquals('9.913', $type->format(9.913));
        $this->assertEquals('', $type->format(''));
        $this->assertEquals('', $type->format(null));
    }

    /** @test */
    public function url()
    {
        $type = new Url();
        $this->assertEquals(env('APP_URL', 'http://localhost') . '/google.com', $type->format('google.com'));
        $this->assertEquals('http://google.com', $type->format('http://google.com'));
    }

    /** @test */
    public function yesNo()
    {
        $type = new YesNo();
        $this->assertEquals('Yes', $type->format(1));
        $this->assertEquals('Yes', $type->format(true));
        $this->assertEquals('Yes', $type->format(['found']));
        $this->assertEquals('No', $type->format([]));
        $this->assertEquals('No', $type->format(false));
        $this->assertEquals('No', $type->format(0));
    }

    /** @test */
    public function yesNoShort()
    {
        $type = new YesNoShort();
        $this->assertEquals('Y', $type->format(1));
        $this->assertEquals('Y', $type->format(true));
        $this->assertEquals('Y', $type->format(['found']));
        $this->assertEquals('N', $type->format([]));
        $this->assertEquals('N', $type->format(false));
        $this->assertEquals('N', $type->format(0));
    }
}
