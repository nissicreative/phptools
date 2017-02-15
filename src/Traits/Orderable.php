<?php
namespace Nissi\Traits;

trait Orderable
{
    /**
     * The item's ID.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The item's price.
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * A short (line item) description of the item.
     */
    public function getShortDescription()
    {
        return $this->description;
    }
}
