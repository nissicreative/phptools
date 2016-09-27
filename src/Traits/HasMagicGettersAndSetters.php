<?php
namespace Nissi\Traits;

use ReflectionClass;
use BadMethodCallException;

trait HasMagicGettersAndSetters
{
    /**
     * Returns the value of the given property, with a default value fallback.
     *
     * @return mixed
     */
    public function getValue($propertyName, $default = null)
    {
        $this->validateProperty($propertyName);

        return $this->$propertyName ?: $default;
    }

    /**
     * Sets the value of the given property.
     *
     * @return $this
     */
    public function setValue($propertyName, $val = null)
    {
        $this->validateProperty($propertyName);

        $this->$propertyName = $val;

        return $this;
    }

    /**
     * Magic method invoked if a method of the same name does not exist.
     *
     * Attempts to retrieve a property's value by using `getFoo($default)` syntax
     * or to set a property's value by using `setFoo($val)` syntax.
     *
     * @throws BadMethodCallException
     * @return mixed
     */
    public function __call($methodName, array $arguments = [])
    {
        $arguments = (array) $arguments;

        // Property name should be snake_case.
        $propertyName = self::snakeCase(substr($methodName, 3));

        // Handle get__ and set__ methods
        switch (strtolower(substr($methodName, 0, 3))) {
            case 'get':
                $default = (isset($arguments[0]) ? $arguments[0] : null);
                return $this->getValue($propertyName, $default);
            case 'set':
                return $this->setValue($propertyName, $arguments[0]);
            default:
                throw new BadMethodCallException(sprintf('`%s` is not a valid method.', $methodName));
        }
    }

    /**
     * Validates the existence of a property and ensures that it is not a private member.
     *
     * @throws BadMethodCallException
     * @return bool
     */
    protected function validateProperty($propertyName)
    {
        // Make sure property exists.
        if ( ! property_exists($this, $propertyName)) {
            throw new BadMethodCallException(sprintf('`%s::%s` is not defined.', get_called_class(), $propertyName));
        }

        // Make sure property is public.
        $property = (new ReflectionClass($this))->getProperty($propertyName);

        if ($property->isPrivate()) {
            throw new BadMethodCallException(sprintf('`%s::%s` is private.', get_called_class(), $propertyName));
        }

        return true;
    }

    /**
     * Converts input to snake_case, which is assumed to be the case of property names
     *
     * @return string
     */
    private static function snakeCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);

        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

}
