<?php
namespace Nissi\ValueObjects;

class PostalAddress extends AbstractValueObject
{
    use \Nissi\Traits\FormatsAddress;

    protected $first_name;
    protected $last_name;
    protected $name;
    protected $attn;
    protected $company;
    protected $organization;
    protected $street;
    protected $address;
    protected $address_1;
    protected $address_2;
    protected $address_3;
    protected $city;
    protected $locality;
    protected $state;
    protected $region;
    protected $zip;
    protected $postal_code;
    protected $country;
    protected $lat;
    protected $lng;
}
