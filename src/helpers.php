<?php

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
