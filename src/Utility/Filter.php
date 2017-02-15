<?php
namespace Nissi\Utility;

class Filter
{
    /**
     * Returns TRUE for "1", "true", "on" and "yes". Returns FALSE otherwise.
     */
    public function isTrue($var, $flags = null)
    {
        return filter_var($var, FILTER_VALIDATE_BOOLEAN, flags);
    }

    /**
     * Validates whether the value is a valid e-mail address.
     */
    public function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validates value as URL (http://www.faqs.org/rfcs/rfc2396), optionally with required components.
     */
    public function isUrl($url, $flags = FILTER_FLAG_HOST_REQUIRED)
    {
        return filter_var($url, FILTER_VALIDATE_URL, $flags);
    }

    /**
     * Validates value as IP address, optionally only IPv4 or IPv6 or not from private or reserved ranges.
     */
    public function isIp($address, $flags = null)
    {
        return filter_var($address, FILTER_VALIDATE_IP, $flags);
    }

    /**
     * Validates that a string is valid JSON
     */
    public function isJson($str)
    {
        return is_string($str) && json_decode($str);
    }

    /**
     * Calculates a "safe" filename. Useful for user-generated uploads.
     */
    public function safeFilename($filename)
    {
        $filename = strtr($filename, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');

        $filename = strtr($filename, [
            'Þ' => 'TH',
            'þ' => 'th',
            'Ð' => 'DH',
            'ð' => 'dh',
            'ß' => 'ss',
            'Œ' => 'OE',
            'œ' => 'oe',
            'Æ' => 'AE',
            'æ' => 'ae',
            'µ' => 'u'
        ]);

        $filename = preg_replace([
            '~\s~',
            '~\.[\.]+~',
            '~[^\w_\.\-]~'
        ], [
            '_',
            '.',
            ''
        ], $filename);

        return $filename;
    }
}
