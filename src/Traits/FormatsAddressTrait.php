<?php
namespace Nissi\Traits;

trait FormatsAddressTrait
{
    /**
     * @param $options
     */
    public function mailingLabel(array $options = [])
    {
        $defaults = [
            'display_country' => false,
            'separator'       => '<br />'
        ];

        $options += $defaults;
        extract($options);

        $lines = [];

        if (method_exists($this, 'name') && $this->name()) {
            $lines[] = $this->name();
        } elseif ( ! empty($this->name)) {
            $lines[] = $this->name;
        }

        if ( ! empty($this->company)) {
            $lines[] = $this->company;
        } elseif ( ! empty($this->organization)) {
            $lines[] = $this->organization;
        }

        if ($this->attn) {
            $lines[] = 'Attn: ' . $this->attn;
        }

        $lines[] = $this->address(compact($options));

        return trim(implode($separator, $lines));
    }

    /**
     * @param $options
     */
    public function address(array $options = [])
    {
        $defaults = [
            'display_country'      => false,
            'separator'            => '<br />',
            'city_state_separator' => ', '
        ];

        $options += $defaults;
        extract($options);

        $lines = [];

        if ( ! empty($this->address)) {
            $lines[] = $this->address;
        } elseif ( ! empty($this->address_1)) {
            $lines[] = $this->address_1;
        }

        if ( ! empty($this->address_2)) {
            $lines[] = $this->address_2;
        }

        if ( ! empty($this->address_3)) {
            $lines[] = $this->address_3;
        }

        if ($this->cityState($city_state_separator)) {
            $lines[] = trim($this->cityState() . ' ' . $this->postalCode());
        }

        if ($display_country) {
            $lines[] = $this->country;
        }

        return implode($separator, $lines);
    }

    /**
     * @param $separator
     */
    public function cityState($separator = ', ')
    {
        $arr = [];

        if ( ! empty($this->city)) {
            $arr[] = $this->city;
        } elseif ( ! empty($this->locality)) {
            $arr[] = $this->locality;
        }

        if ( ! empty($this->state)) {
            $arr[] = $this->state;
        } elseif ( ! empty($this->region)) {
            $arr[] = $this->region;
        }

        return (empty($arr)) ? '' : implode($separator, $arr);
    }

    /**
     * @return mixed
     */
    public function postalCode()
    {
        if ( ! empty($this->zip)) {
            return $this->zip;
        } elseif ( ! empty($this->postal_code)) {
            return $this->postal_code;
        }
    }

}
