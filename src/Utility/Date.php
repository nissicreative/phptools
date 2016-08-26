<?php

namespace Nissi\Utility;

use Carbon\Carbon;

class Date
{
    /**
     * Returns the season in which the provided date falls.
     *
     * @access public
     * @param  mixed    $date (default: null)
     * @return string
     */
    public function season($date = null)
    {
        $c = Carbon::parse($date);

        $dayOfYear = $c->dayOfYear;
        // return $dayOfYear;

        $season_starts = [
            354 => 'winter', // Dec 21
            264 => 'fall',   // Sep 21
            172 => 'summer', // Jun 21
            80  => 'spring', // Mar 21
        ];

        foreach ($season_starts as $day => $season) {
            if ($dayOfYear >= $day) {
                return $season;
            }
        }

        // we must be between Jan 1 and March 31
        return 'winter';
    }

    /**
     * Returns the days of the month as an array.
     *
     * @access public
     * @param  string  $month (default: '1')
     * @param  mixed   $year  (default: null)
     * @return array
     */
    public function daysOfMonth($month = '1', $year = null)
    {
        if ( ! $year) {
            $year = date('Y');
        }

        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $days = [];

        for ($i = 1; $i <= $num_days; $i++) {
            $days[$i] = sprintf('%02d', $i);
        }

        return $days;
    }

    /**
     * Returns the days of the week as an array.
     *
     * @access public
     * @param  string  $keyFormat (default: 'D')
     * @param  string  $valFormat (default: 'l')
     * @return array
     */
    public function daysOfWeek($keyFormat = 'D', $valFormat = 'l')
    {
        $weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        $days = [];

        foreach ($weekDays as $day) {
            $dt  = \DateTime::createFromFormat('D', $day);
            $key = $dt->format($keyFormat);
            $val = $dt->format($valFormat);

            $days[$key] = $val;
        }

        return $days;
    }

    /**
     * Returns months of the year as an array.
     *
     * @access public
     * @param  string  $keyFormat (default: 'm')
     * @param  string  $valFormat (default: 'F (m)')
     * @return array
     */
    public function monthsOfYear($keyFormat = 'm', $valFormat = 'F (m)')
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $dt = new \DateTime();
            $dt->setDate(2000, $i, 1);

            $key = $dt->format($keyFormat);
            $val = $dt->format($valFormat);

            $months[$key] = $val;
        }

        return $months;
    }

    /**
     * Returns an array of years from $start to $end.
     *
     * @access public
     * @param  mixed   $start     (default: null)
     * @param  mixed   $end       (default: null)
     * @param  string  $keyFormat (default: 'Y')
     * @param  string  $valFormat (default: 'Y')
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
            $dt = DateTime::createFromFormat('Y', $val);

            $key = $dt->format($keyFormat);

            $years[$key] = $dt->format($valFormat);
        }

        return $years;
    }
}
