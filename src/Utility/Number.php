<?php
namespace Nissi\Utility;

class Number
{
    /**
     * Formats a value as currency.
     *
     * @access public
     * @param  mixed    $amount
     * @param  array    $options  (default: [])
     * @return string
     */
    public function currency($amount, array $options = [])
    {
        $defaults = [
            'before'    => '',
            'after'     => '',
            'thousands' => '',
            'precision' => 2,
            'decimal'   => '.',
            'zero'      => '0.00'
        ];

        $options += $defaults;
        extract($options);

        if ( ! $amount) {
            return $zero;
        }

        return $before . number_format($amount, $precision, $decimal, $thousands) . $after;
    }

    /**
     * Formats a value as a percentage.
     *
     * @param  float    $amount
     * @param  integer  $precision
     * @param  array    $options
     * @return string
     */
    public function percentage($amount, $precision = 0, array $options = [])
    {
        $defaults = [
            'before'    => '',
            'after'     => '',
            'thousands' => '',
            'decimal'   => '.',
            'zero'      => '0'
        ];

        $options += $defaults;
        extract($options);

        if ( ! $amount) {
            return $zero;
        }

        return $before . number_format($amount * 100, $precision, $decimal, $thousands) . $after;
    }

    /**
     * Formats a number as an English string.
     *
     * @see http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
     *
     * @param  $number
     * @return mixed
     */
    public static function toWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = [
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'forty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        ];

        if ( ! is_numeric($number)) {
            throw new InvalidArgumentException('Invalid number.');
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            throw new InvalidArgumentException(
                'toWords function only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
        }

        if ($number < 0) {
            return $negative . $this->toWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string    = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->toWords($remainder);
                }
                break;
            default:
                $baseUnit     = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder    = $number % $baseUnit;
                $string       = $this->toWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->toWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    /**
     * Formats a number as an ordinal.
     *
     * @param  mixed    $number
     * @return string
     */
    public function ordinal($number)
    {
        $number = (int) $number;
        $ends   = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }
}
