<?php

if (!function_exists('describe')) {
    function describe($description, $callback)
    {
        GivenPHP::get_instance()->describe($description, $callback);
    }
}

if (!function_exists('context')) {
    function context($description, $callback)
    {
        GivenPHP::get_instance()->context($description, $callback);
    }
}

if (!function_exists('given')) {
    function given($name, $value)
    {
        GivenPHP::get_instance()->given($name, $value);
    }
}

if (!function_exists('when')) {
    function when($callback)
    {
        GivenPHP::get_instance()->when($callback);
    }
}

if (!function_exists('then')) {
    function then($callback)
    {
        GivenPHP::get_instance()->then($callback);
    }
}
