<?php
namespace Nissi\Utility;

class Format
{
    /**
     * Formats a US phone number.
     *
     * @return string
     */
    public function phone($phoneNumber, $format = null, array $options = [])
    {
        $defaults = [
            'require_area_code' => true,
            'area_code'         => null,
            'ext'               => '',
            'ext_separator'     => ' x'
        ];

        $options += $defaults;
        extract($options);

        $digits = preg_replace('/\D/', '', $phoneNumber);

        // Optional country code (1), optional area code (first number 2-9), seven digit phone
        if ( ! preg_match('@^(1)?([2-9][0-9]{2})?([0-9]{3})([0-9]{4}$)@', $digits, $matches)) {
            return false;
        }

        $country    = $matches[1];
        $area       = $matches[2];
        $exchange   = $matches[3];
        $subscriber = $matches[4];

        if ( ! $area) {
            if ($require_area_code) {
                return '';
            }
            $area = $area_code;
        }

        $extension = '';
        if ($ext) {
            $extension = $ext_separator . $ext;
        }

        switch ($format) {
            case 'parentheses':    // (###) ###-####
                $output = ($area) ? "({$area}) " : '';
                $output .= "{$exchange}-{$subscriber}{$extension}";
                break;
            case 'dashes':    // ###-###-####
                $output = ($area) ? "{$area}-" : '';
                $output .= "{$exchange}-{$subscriber}{$extension}";
                break;
            case 'dots':    // ###.###.####
                $output = ($area) ? "{$area}." : '';
                $output .= "{$exchange}.{$subscriber}{$extension}";
                break;
            case 'digits':    // ##########
                $output = "{$area}{$exchange}{$subscriber}{$extension}";
                break;
            case 'intl':// +1##########
            default:
                $output = "+1{$area}{$exchange}{$subscriber}{$extension}";
        }

        return $output;
    }

    /**
     * Formats a US ZIP code.
     *
     * @access public
     * @param  mixed    $zip
     * @param  bool     $plusFour (default: false)
     * @return string
     */
    public function zip($zip, $plusFour = false)
    {
        $digits = preg_replace('/\D/', '', $zip);

        if (strlen($digits) == 5 || strlen($digits) == 0) {
            return $digits;
        } elseif (strlen($digits) == 9) {
            return ($plusFour) ? substr($digits, 0, 5) . '-' . substr($digits, 5) : substr($digits, 0, 5);
        }

        return $zip;
    }

    /**
     * Formats a URL.
     *
     * @access public
     * @static
     * @param  array    $options (default: [])
     * @return string
     */
    public function url($url, array $options = [])
    {
        if (empty($url)) {
            return '';
        }

        $defaults = [
            'use_scheme'   => true,
            'use_hostname' => true,
            'use_path'     => true,
            'use_query'    => false,
            'use_fragment' => false
        ];

        $options += $defaults;
        extract($options);

        $pieces = parse_url($url);
        extract($pieces); // Potentially: scheme, host, port, user, pass, path, query, fragment

        $is_schemeless = false;
        if (preg_match('~^//~', $url)) {
            $is_schemeless = true;
        }

        if (empty($scheme)) {
            $scheme = 'http';
        }

        $str = '';

        if ($use_scheme) {
            $str .= ($is_schemeless) ? '//' : strtolower($scheme) . '://';
        }

        if (empty($host)) {
            $host = $path;
            $path = null;
        }

        $str .= strtolower($host);

        if ($use_path &&  ! empty($path)) {
            $str .= $path;
        }

        if ($use_query &&  ! empty($query)) {
            $str .= "?$query";
        }

        if ($use_fragment &&  ! empty($fragment)) {
            $str .= $fragment;
        }

        $normalized = $scheme . '://' . $host;
        if ( ! filter_var($normalized, FILTER_VALIDATE_URL)) {
            return '';
        }

        return $str;
    }

    /**
     * Returns a friendly representation of data size
     *
     * @see    http://www.trevorsimonton.com/blog/file-size-php-bytes-user-friendly
     *
     * @param  integer  $bytes       number of bytes
     * @param  integer  $precision
     * @return string
     */
    public function bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Intelligently formats a person's name
     *
     * @see    http://www.media-division.com/correct-name-capitalization-in-php/
     *
     * @param  string $string  input text
     * @return string output
     */
    public function personName($string)
    {
        $wordSplitters       = [' ', '-', "O'", "L'", "D'", 'St.', 'Mc'];
        $lowercaseExceptions = ['the', 'van', 'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', "l'", "d'"];
        $uppercaseExceptions = ['III', 'IV', 'VI', 'VII', 'VIII', 'IX'];

        $string = strtolower($string);

        foreach ($wordSplitters as $delimiter) {
            $words    = explode($delimiter, $string);
            $newwords = [];

            foreach ($words as $word) {
                if (in_array(strtoupper($word), $uppercaseExceptions)) {
                    $word = strtoupper($word);
                } elseif ( ! in_array($word, $lowercaseExceptions)) {
                    $word = ucfirst($word);
                }

                $newwords[] = $word;
            }

            if (in_array(strtolower($delimiter), $lowercaseExceptions)) {
                $delimiter = strtolower($delimiter);
            }

            $string = implode($delimiter, $newwords);
        }

        return $string;
    }
}
