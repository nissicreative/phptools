<?php
namespace Nissi\Traits;

trait Orderable
{
    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getShortDescription()
    {
        return $this->description;
    }
}
