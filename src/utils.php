<?php

use GivenPHP\GivenPHP;

if (!function_exists('describe')) {
    /**
     * @see GivenPHP::describe
     *
     * @param $description
     * @param $callback
     *
     * @return void
     */
    function describe($description, $callback)
    {
        GivenPHP::getInstance()->describe($description, $callback);
    }
}

if (!function_exists('context')) {
    /**
     * @see GivenPHP::context
     *
     * @param $description
     * @param $callback
     *
     * @return void
     */
    function context($description, $callback)
    {
        GivenPHP::getInstance()->context($description, $callback);
    }
}

if (!function_exists('given')) {
    /**
     * @see GivenPHP::given
     *
     * @param $name
     * @param $value
     *
     * @return void
     */
    function given($name, $value)
    {
        GivenPHP::getInstance()->given($name, $value);
    }
}

if (!function_exists('when')) {
    /**
     * @see GivenPHP::when
     *
     * @param $name
     * @param $callback
     *
     * @return void
     */
    function when($name, $callback = GivenPHP::EMPTY_VALUE)
    {
        if ($callback === GivenPHP::EMPTY_VALUE) {
            $callback = $name;
            $name     = null;
        }

        GivenPHP::getInstance()->when($name, $callback);
    }
}

if (!function_exists('then')) {
    /**
     * @see GivenPHP::then
     *
     * @param string $callback
     *
     * @return void
     */
    function then($callback)
    {
        GivenPHP::getInstance()->then($callback);
    }
}
