<?php

if (! function_exists('set_default')) {
    /**
     * Set a default value for a specified property of an object if it is "blank".
     * The default value can be a direct value or a closure.
     *
     * @param  mixed  $object
     * @param  mixed|callable  $defaultValue
     * @return mixed
     */
    function set_default($object, string $property, $defaultValue)
    {
        return tap($object, function ($instance) use ($property, $defaultValue) {
            if (blank(data_get($instance, $property))) {
                $value = $defaultValue instanceof Closure ? $defaultValue($instance) : $defaultValue;
                data_set($instance, $property, $value);
            }
        });
    }
}

if (! function_exists('str_boolean')) {
    function str_boolean(bool|callable $expression)
    {
        if (is_callable($expression)) {
            return $expression() === true ? 'true' : 'false';
        } else {
            return $expression === true ? 'true' : 'false';
        }
    }
}
