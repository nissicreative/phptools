<?php
namespace Nissi\Utility;

class Text
{
    /**
     * @param $string
     */
    public function entities($string)
    {
        $string = $this->convertWindows1252(trim($string));
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');

        if (defined('ENT_SUBSTITUTE')) {
            // PHP 5.4+
            return htmlentities($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
        } else {
            return htmlentities($string, ENT_QUOTES | ENT_IGNORE, 'UTF-8', false);
        }
    }

    /**
     * @param $string
     */
    public function specialchars($string)
    {
        $string = $this->convertWindows1252(trim($string));
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');

        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
    }

    public function normalizeWhitespace($string)
    {
        // Force double returns
        // $string = preg_replace('/[\r\n]/', "\n\n", $string);

        $string = trim($string);
        $string = $this->removeExtraNewlines($string);
        $string = $this->removeExtraSpaces($string);
        $string = $this->trimLines($string);

        return $string;
    }

    public function removeExtraSpaces($string)
    {
        $string = trim($string);

        // Remove extra spaces and non-breaking spaces
        $string = preg_replace('/[ \x{00A0}]{2,}/u', ' ', $string);

        return $string;
    }

    public function removeExtraNewlines($string)
    {
        $string = trim($string);

        // Remove extra returns
        $string = preg_replace('/[\r\n]{2,}/', "\n\n", $string);

        return $string;
    }

    public function trimLines($string)
    {
        // Trim each line
        $string = implode("\n", array_map('trim', explode("\n", $string)));

        return $string;
    }

    public function smartenQuotes($string)
    {
        // Opening singles
        $string = preg_replace('/(^|[-\x{2014}\(\[{\s])\'/u', '$1‘', $string);

        // Closing singles
        $string = str_replace("'", '’', $string);

        // Opening doubles
        $string = preg_replace('/(^|[-\x{2014}\(\[{\x{2018}\s])"/u', '$1“', $string);

        // Closing doubles
        $string = str_replace('"', '”', $string);

        return $string;
    }

    /**
     * @param  $string
     * @return mixed
     */
    public function convertWindows1252($string)
    {
        $encoding = mb_detect_encoding($string);
        if ($encoding != 'cp1252') {
            return $string;
        }

        // Create lookbehind RegEx to make sure code points are not preceded by UTF-8 start byte
        // $sb = Valid UTF-8 Start Bytes
        $sb     = '/(?<![\xC0-\xDF])';
        $search = [
            $sb . '\x80/', // Euro
            $sb . '\x82/', // Low Single Curved Quote
            $sb . '\x83/', // F with Hook
            $sb . '\x84/', // Low Double Curved Quote
            $sb . '\x85/', // Ellipsis
            $sb . '\x86/', // Dagger
            $sb . '\x87/', // Double Dagger
            $sb . '\x88/', // Circumflex
            $sb . '\x89/', // Permille
            $sb . '\x8B/', // Left Single Angle Quote
            $sb . '\x91/', // Left Single Quote
            $sb . '\x92/', // Right Single Quote
            $sb . '\x93/', // Left Double Quote
            $sb . '\x94/', // Right Double Quote
            $sb . '\x95/', // Bullet
            $sb . '\x96/', // En-Dash
            $sb . '\x97/', // Em-Dash
            $sb . '\x98/', // Tilde
            $sb . '\x99/', // Trademark
            $sb . '\x9B/', // Right Single Angle Quote
            $sb . '\xA0/', // Non-breaking Space
        ];

        $replace = [
            '&euro;',
            '&sbquo;',
            'ƒ',
            '&bdquo;',
            '&hellip;',
            '&dagger;',
            '&Dagger;',
            'ˆ',
            '&permil;',
            '&lsaquo;',
            '&lsquo;',
            '&rsquo;',
            '&ldquo;',
            '&rdquo;',
            '&bull;',
            '&ndash;',
            '&mdash;',
            '&tilde;',
            '&trade;',
            '&rsaquo;',
            '&nbsp;',
        ];

        return preg_replace($search, $replace, $string);
    }

    /**
     * @param $string
     */
    public function smarten($string)
    {
        $search = [
            ' - ',
            '--',
            '...',
        ];

        $replace = [
            '–', // en-dash
            '—', // em-dash
            '…', // ellipsis
        ];

        return str_replace($search, $replace, $string);
    }

    /**
     * @param $text
     * @param $length
     * @param $options
     */
    public function truncate($text, $length = 20, array $options = [])
    {
        $defaults = [
            'ellipsis'    => '...',
            'exact'       => true,
            'punctuation' => '.!?:;,-',
        ];

        $options += $defaults;
        extract($options);

        $text = $this->entities($text);
        $text = strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));

        if (strlen($text) <= $length) {
            return $this->specialchars($text);
        }

        $text = substr($text, 0, $length);

        if ( ! $exact) {
            $text = substr($text, 0, strrpos($text, ' '));
        }

        $text = (strspn(strrev($text), $punctuation) != 0) ? substr($text, 0, -strspn(strrev($text), $punctuation)) : $text;

        return $this->specialchars($text) . $ellipsis;
    }

    /**
     * @param  $text       String to transform
     * @param  $length     Desired length of
     * @param  $separator  Characters to use in middle
     * @return string
     */
    public function mtruncate($text, $length = 20, $separator = '…')
    {
        $text       = strip_tags(html_entity_decode($this->entities($text), ENT_QUOTES, 'UTF-8'));
        $textLength = strlen($text);

        if ($textLength <= $length) {
            return $this->specialchars($text);
        }

        $ellipsesLength = strlen($separator);

        $firstPart  = substr($text, 0, ceil($length / 2) - floor($ellipsesLength / 2));

        $secondPart = strrev(substr(strrev($text), 0, floor($length / 2) - ceil($ellipsesLength / 2)));

        return $this->specialchars($firstPart . $separator . $secondPart);
    }

    //! Case Conversions
    // ================================================== //
    /**
     * @param $string
     */
    public function capitalize($string)
    {
        if ($string != mb_convert_case($string, MB_CASE_LOWER) && $string != mb_convert_case($string, MB_CASE_UPPER)) {
            return $this->specialchars($string);
        } else {
            return $this->specialchars(mb_convert_case($string, MB_CASE_TITLE));
        }
    }

    /**
     * @param  $title
     * @return mixed
     */
    public function titleCase($title)
    {
        //original Title Case script © John Gruber <daringfireball.net>
        //javascript port © David Gouch <individed.com>
        //PHP port of the above by Kroc Camen <camendesign.com>

        //remove HTML, storing it for later
        //       HTML elements to ignore    | tags  | entities
        $regx = '/<(code|var)[^>]*>.*?<\/\1>|<[^>]+>|&\S+;/';
        preg_match_all($regx, $title, $html, PREG_OFFSET_CAPTURE);
        $title = preg_replace($regx, '', $title);

        //find each word (including punctuation attached)
        preg_match_all('/[\w\p{L}&`\'‘’"“\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE);
        foreach ($m1[0] as &$m2) {
            //shorthand these- "match" and "index"
            list($m, $i) = $m2;

            //correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
            //we fix this by recounting the text before the offset using multi-byte aware `strlen`
            $i = mb_strlen(substr($title, 0, $i), 'UTF-8');

            //find words that should always be lowercase…
            //(never on the first word, and never if preceded by a colon)
            $m = $i > 0 && mb_substr($title, max(0, $i - 2), 1, 'UTF-8') !== ':' &&
            ! preg_match('/[\x{2014}\x{2013}] ?/u', mb_substr($title, max(0, $i - 2), 2, 'UTF-8')) &&
            preg_match('/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m)
                ? //…and convert them to lowercase
            mb_strtolower($m, 'UTF-8')

            //else: brackets and other wrappers
             : (preg_match('/[\'"_{(\[‘“]/u', mb_substr($title, max(0, $i - 1), 3, 'UTF-8'))
                    ? //convert first letter within wrapper to uppercase
                mb_substr($m, 0, 1, 'UTF-8') .
                mb_strtoupper(mb_substr($m, 1, 1, 'UTF-8'), 'UTF-8') .
                mb_substr($m, 2, mb_strlen($m, 'UTF-8') - 2, 'UTF-8')

                //else: do not uppercase these cases
                 : (preg_match('/[\])}]/', mb_substr($title, max(0, $i - 1), 3, 'UTF-8')) ||
                    preg_match('/[A-Z]+|&|\w+[._]\w+/u', mb_substr($m, 1, mb_strlen($m, 'UTF-8') - 1, 'UTF-8'))
                        ? $m
                    //if all else fails, then no more fringe-cases; uppercase the word
                     : mb_strtoupper(mb_substr($m, 0, 1, 'UTF-8'), 'UTF-8') .
                    mb_substr($m, 1, mb_strlen($m, 'UTF-8'), 'UTF-8')
                ));

            //resplice the title with the change (`substr_replace` is not multi-byte aware)
            $title = mb_substr($title, 0, $i, 'UTF-8') . $m .
            mb_substr($title, $i + mb_strlen($m, 'UTF-8'), mb_strlen($title, 'UTF-8'), 'UTF-8')
            ;
        }

        //restore the HTML
        foreach ($html[0] as &$tag) {
            $title = substr_replace($title, $tag[0], $tag[1], 0);
        }

        return $title;
    }

    /**
     * @param $input_string
     */
    public function sentenceCase($input_string)
    {
        $sentences     = preg_split('/([.?!]+)/', $input_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $output_string = '';

        foreach ($sentences as $key => $sentence) {
            $output_string .= ($key & 1) == 0 ? ucfirst(strtolower(trim($sentence))) : $sentence . ' ';
        }

        return $this->specialchars(trim($output_string));
    }

}
