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

    /**
     * Static factory method.
     */
    public static function create(array $data = [])
    {
        return new static($data);
    }

    /**
     * Return properties as an array.
     */
    public function toArray($options = null)
    {
        // Pass in an array of property names to get only those properties.
        if (is_array($options)) {
            return collect(get_object_vars($this))
                ->filter(function ($val, $key) use ($options) {
                    return in_array($key, $options);
                })
                ->toArray();
        }

        // Pass in a "truthy" value to get all properties.
        if ($options) {
            return get_object_vars($this);
        }

        // Default to returning only non-null values.
        return collect(get_object_vars($this))
            ->reject(function ($var) {
                return is_null($var);
            })
            ->toArray();
    }

}
