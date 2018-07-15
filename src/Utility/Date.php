<?php
namespace Nissi\Utility;

use Carbon\Carbon;

class Date
{
    /**
     * Returns the season in which the provided date falls.
     *
     * @return string
     */
    public function season($date = null)
    {
        $c = Carbon::parse($date);

        $dayOfYear = $c->dayOfYear;

        $seasonStarts = [
            354 => 'winter', // Dec 21
            264 => 'fall',   // Sep 21
            172 => 'summer', // Jun 21
            80 => 'spring',  // Mar 21
        ];

        foreach ($seasonStarts as $day => $season) {
            if ($dayOfYear >= $day) {
                return $season;
            }
        }

        // We must be between Jan 1 and March 31.
        return 'winter';
    }

    /**
     * Returns the days of the month as an array.
     *
     * @return array
     */
    public function daysOfMonth($month = '1', $year = null)
    {
        if ( ! $year) {
            $year = date('Y');
        }

        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $days = [];

        for ($i = 1; $i <= $numDays; $i++) {
            $days[$i] = sprintf('%02d', $i);
        }

        return $days;
    }

    /**
     * Returns the days of the week as an array.
     *
     * @return array
     */
    public function daysOfWeek($keyFormat = 'D', $valFormat = 'l', array $days = null)
    {
        if ( ! is_array($days)) {
            $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        }

        return collect($days)
            ->keyBy(function ($day) use ($keyFormat) {
                $carbon = Carbon::createFromFormat('D', $day);
                return $carbon->format($keyFormat);
            })
            ->map(function ($day) use ($valFormat) {
                $carbon = Carbon::createFromFormat('D', $day);
                return $carbon->format($valFormat);
            })
            ->toArray();
    }

    /**
     * Returns months of the year as an array.
     *
     * @return array
     */
    public function monthsOfYear($keyFormat = 'm', $valFormat = 'F (m)')
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $carbon = new Carbon();
            $carbon->setDate(2000, $i, 1);

            $key = $carbon->format($keyFormat);
            $val = $carbon->format($valFormat);

            $months[$key] = $val;
        }

        return $months;
    }

    /**
     * Returns an array of years from $start to $end.
     *
     * @return array
     */
    public function years($start = null, $end = null, $keyFormat = 'Y', $valFormat = 'Y')
    {
        if (empty($start)) {
            $start = date('Y');
        }

        if (empty($end)) {
            $end = date('Y') + 20;
        }

        $values = range($start, $end);

        $years = [];

        foreach ($values as $val) {
            $carbon = Carbon::createFromFormat('Y', $val);

            $key = $carbon->format($keyFormat);

            $years[$key] = $carbon->format($valFormat);
        }

        return $years;
    }
}
