<?php
namespace Nissi\ValueObjects;

use Nissi\Traits\FormatsName;
use Nissi\Traits\FormatsPhone;
use Nissi\Traits\FormatsAddress;

class PostalAddress extends AbstractValueObject
{
    use FormatsAddress, FormatsPhone, FormatsName;

    protected $first_name;
    protected $last_name;
    protected $name;
    protected $attn;
    protected $company;
    protected $organization;
    protected $street;
    protected $street1;
    protected $street2;
    protected $street_1;
    protected $street_2;
    protected $address;
    protected $address1;
    protected $address2;
    protected $address3;
    protected $address_1;
    protected $address_2;
    protected $address_3;
    protected $city;
    protected $locality;
    protected $state;
    protected $region;
    protected $zip;
    protected $zip4;
    protected $postal_code;
    protected $country;
    protected $lat;
    protected $lng;
    protected $phone;
    protected $email;

    /**
     * By default, use mailing label when casting to string.
     */
    public function __toString()
    {
        return $this->getMailingLabel();
    }
}
