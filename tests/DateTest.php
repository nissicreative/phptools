<?php
use Nissi\Utility\Date;
use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

class DateTest extends TestCase
{
    protected $date;

    public function setUp()
    {
        $this->date = new Date();
    }

    /**
     * @dataProvider dateProvider
     */
    public function testSeason($date, $expected)
    {
        $season = $this->date->season($date);
        $this->assertEquals($expected, $season);
    }

    public function dateProvider()
    {
        return [
            [Carbon::create(2016, 1, 1), 'winter'],
            ['2016-03-21', 'spring'],
            ['2016-06-21', 'summer'],
            ['2016-09-21', 'fall'],
            ['2016-12-21', 'winter'],
        ];
    }

    public function testDaysOfMonth() {}
}
