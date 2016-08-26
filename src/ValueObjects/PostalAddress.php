<?php
namespace Nissi\ValueObjects;

class PostalAddress extends AbstractValueObject
{
    use \Nissi\Traits\FormatsAddressTrait;

    protected $first_name;
    protected $last_name;
    protected $name;
    protected $attn;
    protected $company;
    protected $organization;
    protected $address;
    protected $address_2;
    protected $address_3;
    protected $city;
    protected $state;
    protected $zip;
    protected $postal_code;
    protected $country;
    protected $lat;
    protected $lng;
}
