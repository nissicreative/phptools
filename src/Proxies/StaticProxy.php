<?php

namespace Nissi\Proxies;

class StaticProxy
{
    protected static $referenceClass;

    /**
     * @param  $method
     * @param  $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static::$referenceClass();

        if ( ! $instance) {
            throw new RuntimeException(static::$referenceClass . ': Class not found.');
        }

        switch (count($args)) {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:;
                return call_user_func_array([$instance, $method], $args);
        }
    }
}
