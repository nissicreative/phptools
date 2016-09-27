<?php
namespace Nissi\ValueObjects;

use Nissi\Traits\HasMagicGettersAndSetters;

abstract class AbstractValueObject
{
    use HasMagicGettersAndSetters;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
