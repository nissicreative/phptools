<?php

namespace Nissi\Traits;

use Nissi\Proxies\Format;

trait FormatsName
{
    /**
     * Formats a person's name from the supplied components
     *
     * @return string
     */
    public function getFormattedName($components = ['first_name', 'last_name'])
    {
        $props = [];

        // Most commonly used components of the name "body"
        $standard = ['first_name', 'nickname', 'middle_name', 'mi', 'last_name'];

        $nameString = ''; // Output string

        // Name prefix - i.e. Mr., Mrs., Dr,. Rev.
        if (in_array('prefix', $components) && $this->getPrefix()) {
            $nameString .= $this->getPrefix() . ' ';
        }

        // Build "main" part of name
        $middleParts = array_intersect($standard, $components);

        foreach ($middleParts as $property) {
            if ( ! empty($this->$property)) {
                $props[] = Format::personName($this->$property);
            }
        }

        $nameString .= implode(' ', $props);

        // Trailing bits
        if (in_array('suffix', $components) && $this->getSuffix()) {
            $nameString .= ", {$this->getSuffix()}";
        }

        if (in_array('credentials', $components) && $this->getCredentials()) {
            $nameString .= ", {$this->getCredentials()}";
        }

        return trim($nameString);
    }

    /*
    |--------------------------------------------------------------------------
    | Pre-formatted Options
    |--------------------------------------------------------------------------
     */

    /**
     * Returns first/nickname, last, and optional suffix
     */
    public function getName($suffix = false)
    {
        $str = $this->getNickname() . ' ' . $this->getLastName();

        if ($suffix && $this->getSuffix()) {
            $str .= ', ' . $this->getSuffix();
        }

        return Format::personName($str);
    }

    /**
     * Returns (optional) prefix, first, middle, last, and suffix
     */
    public function getFullName($prefix = false)
    {
        $components = ['first_name', 'mi', 'middle_name', 'last_name', 'suffix'];

        if ($prefix) {
            $components[] = 'prefix';
        }

        return $this->getFormattedName($components);
    }

    /**
     * Returns full name (with prefix) and credentials
     */
    public function getFormalName($separator = ', ')
    {
        $nameString = $this->getFullName(true);

        if ($this->getCredentials()) {
            $nameString .= $separator . $this->getCredentials();
        }

        return $nameString;
    }

    /*
    |--------------------------------------------------------------------------
    | Individual Components
    |--------------------------------------------------------------------------
     */

    /**
     * Returns the prefix: i.e. Dr., Mr., Mrs., if present
     */
    public function getPrefix()
    {
        if (empty($this->prefix)) {
            return '';
        }

        return Format::personName($this->prefix);
    }

    /**
     * Returns the first name, if present
     */
    public function getFirstName()
    {
        if (empty($this->first_name)) {
            return '';
        }

        return Format::personName($this->first_name);
    }

    /**
     * Returns the nickname if present, first name otherwise
     */
    public function getNickname()
    {
        if ( ! empty($this->nickname)) {
            return Format::personName($this->nickname);
        }

        return $this->getFirstName();
    }

    /**
     * Returns the middle name, if present
     */
    public function getMiddleName()
    {
        if (empty($this->middle_name)) {
            return '';
        }

        return Format::personName($this->middle_name);
    }

    /**
     * Returns the middle initial, if present
     */
    public function getMi()
    {
        if (empty($this->mi)) {
            return '';
        }

        return Format::personName($this->mi);
    }

    /**
     * Returns the last name, if present
     */
    public function getLastname()
    {
        if (empty($this->last_name)) {
            return '';
        }

        return Format::personName($this->last_name);
    }

    /**
     * Returns the suffix, if present
     */
    public function getSuffix()
    {
        if (empty($this->suffix)) {
            return '';
        }

        return trim($this->suffix);
    }

    /**
     * Returns the credentials: i.e. MBA, JD, PhD, if present
     */
    public function getCredentials()
    {
        if (empty($this->credentials)) {
            return '';
        }

        return $this->credentials;
    }

    /**
     * Returns person's initials with separator and appends.
     */
    public function getInitials($separator = '', $appends = '', $useMiddle = true)
    {
        $arr = [];

        if ($first = $this->getFirstName()) {
            $arr[] = substr($first, 0, 1);
        }

        if ($middle = $this->getMiddleName() && $useMiddle) {
            $arr[] = substr($middle, 0, 1);
        }

        if ($last = $this->getLastName()) {
            $arr[] = substr($last, 0, 1);
        }

        return trim(implode($separator, $arr)) . $appends;
    }

    /*
    |--------------------------------------------------------------------------
    | Laravel Accessors
    |--------------------------------------------------------------------------
     */

    /**
     * Value to return for $this->name
     */
    public function getNameAttribute()
    {
        return $this->getName();
    }

    /**
     * Formatted First Name.
     */
    public function getFirstNameAttribute($val)
    {
        return Format::personName($val);
    }

    /**
     * Formatted Last Name.
     */
    public function getLastNameAttribute($val)
    {
        return Format::personName($val);
    }

    /**
     * Value to return for $this->fullname
     */
    public function getFullnameAttribute()
    {
        return $this->getFullname();
    }

    /**
     * Value to return for $this->formal_name
     */
    public function getFormalNameAttribute()
    {
        return $this->getFormalName();
    }

    /**
     * Value to return for $this->nickname
     */
    public function getNicknameAttribute()
    {
        return $this->getNickname();
    }
}
