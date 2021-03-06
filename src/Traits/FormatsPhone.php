<?php
namespace Nissi\Traits;

trait FormatsPhone
{
    /**
     * Retrieve the phone attribute.
     */
    public function getPhoneAttribute($val)
    {
        if (empty($this->attributes['phone'])) {
            return;
        }

        return format_phone($this->attributes['phone']);
    }

    /**
     * Retrieve the fax attribute.
     */
    public function getFaxAttribute($val)
    {
        if (empty($this->attributes['fax'])) {
            return;
        }

        return format_phone($this->attributes['fax']);
    }

    /**
     * Format the phone attribute when setting its value.
     */
    public function setPhoneAttribute($val)
    {
        $this->attributes['phone'] = format_phone($val, 'digits');
    }

    /**
     * Format the fax attribute when setting its value.
     */
    public function setFaxAttribute($val)
    {
        $this->attributes['fax'] = format_phone($val, 'digits');
    }
}
