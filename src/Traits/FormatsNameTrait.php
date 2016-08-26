<?php

namespace Nissi\Traits;

use Nissi\Proxies\Format;

trait FormatsNameTrait
{
    public function formattedName($components = ['first_name', 'last_name'])
    {
        // Most commonly used components of the name "body"
        $standard = ['first_name', 'nickname', 'middle_name', 'mi', 'last_name'];

        $nameString = ''; // Output string

        // Name prefix - i.e. Mr., Mrs., Dr,. Rev.
        if (in_array('prefix', $components)) {
            $nameString .= $this->prefix() . ' ';
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
        if (in_array('suffix', $components) && ( ! empty($this->suffix))) {
            $nameString .= ", {$this->suffix}";
        }

        if (in_array('credentials', $components) && ( ! empty($this->credentials))) {
            $nameString .= ", {$this->credentials}";
        }

        return $nameString;
    }

   /*
   |--------------------------------------------------------------------------
   | Pre-formatted Options
   |--------------------------------------------------------------------------
   */
    public function name($suffix = false)
    {
        $str = $this->nickname() . ' ' . $this->last_name;

        if ($suffix && $this->suffix) {
            $str . ', ' . $this->suffix;
        }

        return Format::personName($str);
    }


    public function fullname($suffix = false)
    {
        $components = ['first_name', 'mi', 'middle_name', 'last_name', 'suffix'];

        if ($suffix) {
            $components[] = $this->suffix();
        }

        return $this->formattedName($components);
    }

    public function formalname()
    {
        $nameString = $this->fullname(true);

        if ($this->prefix()) {
            $nameString = $this->prefix() . ' ' . $nameString;
        }

        if ($this->credentials()) {
            $nameString .= ', ' . $this->credentials();
        }

        return $nameString;
    }


    /*
    |--------------------------------------------------------------------------
    | Individual Components
    |--------------------------------------------------------------------------
    */
    public function prefix()
    {
        if (empty($this->prefix)) {
            return '';
        }

        return Format::personName($this->prefix);
    }

    public function firstname()
    {
        if (empty($this->first_name)) {
            return '';
        }
        return Format::personName($this->first_name);
    }

    public function nickname()
    {
        if (!empty($this->nickname)) {
            return Format::personName($this->nickname);
        }

        return $this->firstname();
    }

    public function middlename()
    {
        if (empty($this->middle_name)) {
            return '';
        }

        return Format::personName($this->middle_name);
    }

    public function mi()
    {
        if (empty($this->mi)) {
            return '';
        }

        return Format::personName($this->mi);
    }

    public function lastname()
    {
        if (empty($this->last_name)) {
            return '';
        }

        return Format::personName($this->last_name);
    }

    public function suffix()
    {
        if (empty($this->suffix)) {
            return '';
        }

        return Format::personName($this->suffix);
    }

    public function credentials()
    {
        if (empty($this->credentials)) {
            return '';
        }

        return $this->credentials;
    }

}
