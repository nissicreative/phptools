<?php
namespace Nissi\Traits;

use Nissi\Proxies\Format;

trait FormatsAddress
{
    /**
     * Formats a mailing label: Name, attn, full address
     *
     * @return string
     */
    public function getMailingLabel(array $options = [])
    {
        $defaults = [
            'display_country'      => false,
            'separator'            => '<br />',
            'city_state_separator' => ', '
        ];

        $options += $defaults;
        extract($options);

        $lines = [];

        if ($this->getRecipient()) {
            $lines[] = $this->getRecipient();
        }

        if ($this->getOrganization()) {
            $lines[] = $this->getOrganization();
        }

        if ($this->getAttn()) {
            $lines[] = 'Attn: ' . $this->getAttn();
        }

        $lines[] = $this->getAddress(compact($options));

        return trim(implode($separator, $lines));
    }

    /**
     * Formats an address: street, city, state, ZIP by default
     *
     * @return string
     */
    public function getAddress(array $options = [])
    {
        $defaults = [
            'display_country'      => false,
            'separator'            => '<br />',
            'city_state_separator' => ', '
        ];

        $options += $defaults;
        extract($options);

        $lines = [];

        if ($this->getAddress1()) {
            $lines[] = $this->getAddress1();
        }

        if ($this->getAddress2()) {
            $lines[] = $this->getAddress2();
        }

        if ($this->getAddress3()) {
            $lines[] = $this->getAddress3();
        }

        if ($this->getCityState($city_state_separator)) {
            $lines[] = trim($this->getCityState() . ' ' . $this->getPostalCode());
        }

        if ($display_country && $this->getCountry()) {
            $lines[] = $this->getCountry();
        }

        return trim(implode($separator, $lines));
    }

    /**
     * Formats the city, state line of an address
     *
     * @return string
     */
    public function getCityState($separator = ', ')
    {
        $arr = [];

        if ($this->getLocality()) {
            $arr[] = $this->getLocality();
        }

        if ($this->getRegion()) {
            $arr[] = $this->getRegion();
        }

        return (empty($arr)) ? '' : implode($separator, $arr);
    }

    /*
    |--------------------------------------------------------------------------
    | Individual Components
    |--------------------------------------------------------------------------
     */

    /**
     * Name of person or entity
     */
    public function getRecipient()
    {
        if (method_exists($this, 'getName')) {
            return $this->getName();
        }

        if ( ! empty($this->name)) {
            return trim($this->name);
        }

        if ( ! empty($this->first_name) &&  ! empty($this->last_name)) {
            return $this->first_name . ' ' . $this->last_name;
        }
    }

    /**
     * Organization or company name
     */
    public function getOrganization()
    {
        if ( ! empty($this->organization)) {
            return trim($this->organization);
        }

        if ( ! empty($this->company)) {
            return trim($this->company);
        }
    }

    /**
     * "Attn:" recipient
     */
    public function getAttn()
    {
        if ( ! empty($this->attn)) {
            return trim($this->attn);
        }

        if ( ! empty($this->attention)) {
            return trim($this->attention);
        }
    }

    /**
     * Address line 1
     */
    public function getAddress1()
    {
        if ( ! empty($this->street)) {
            return trim($this->street);
        }

        if ( ! empty($this->street1)) {
            return trim($this->street1);
        }

        if ( ! empty($this->address1)) {
            return trim($this->address1);
        }

        if ( ! empty($this->address_1)) {
            return trim($this->address_1);
        }

        if ( ! empty($this->address)) {
            return trim($this->address);
        }
    }

    /**
     * Address line 2
     */
    public function getAddress2()
    {
        if ( ! empty($this->street2)) {
            return trim($this->street2);
        }

        if ( ! empty($this->address2)) {
            return trim($this->address2);
        }

        if ( ! empty($this->address_2)) {
            return trim($this->address_2);
        }
    }

    /**
     * Address line 3
     */
    public function getAddress3()
    {
        if ( ! empty($this->address3)) {
            return trim($this->address3);
        }

        if ( ! empty($this->address_3)) {
            return trim($this->address_3);
        }
    }

    /**
     * City or locality
     */
    public function getLocality()
    {
        if ( ! empty($this->city)) {
            return trim($this->city);
        }

        if ( ! empty($this->locality)) {
            return trim($this->locality);
        }
    }

    /**
     * State or region
     */
    public function getRegion()
    {
        if ( ! empty($this->state)) {
            return trim($this->state);
        }

        if ( ! empty($this->region)) {
            return trim($this->region);
        }
    }

    /**
     * ZIP or postal code
     */
    public function getPostalCode($plusFour = false)
    {
        if ( ! empty($this->zip)) {
            return Format::zip($this->zip, $plusFour);
        }

        if ( ! empty($this->postal_code)) {
            return trim($this->postal_code);
        }
    }

    /**
     * ZIP only
     */
    public function getZip($plusFour = false)
    {
        if ( ! empty($this->zip)) {
            return Format::zip($this->zip, $plusFour);
        }
    }

    /**
     * 5-digit ZIP only
     */
    public function getZip5()
    {
        return $this->getZip();
    }

    /**
     * 4-digit ZIP add-on code
     */
    public function getZip4()
    {
        if ( ! empty($this->zip4)) {
            return trim($this->zip4);
        }

        $zip = $this->getZip(true);

        if (strpos($zip, '-') !== false) {
            return substr($zip, -4);
        }
    }

    /**
     * Country
     */
    public function getCountry()
    {
        if ( ! empty($this->country)) {
            return trim($this->country);
        }
    }
}
