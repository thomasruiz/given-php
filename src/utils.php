<?php

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
        GivenPHP::get_instance()->describe($description, $callback);
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
        GivenPHP::get_instance()->context($description, $callback);
    }
}

if (!function_exists('given')) {
    /**
     * @see GivenPHP::given
     *
     * @param $name
     * @param $value
     * @param $is_parsed
     *
     * @return void
     */
    function given($name, $value, $is_parsed = false)
    {
        GivenPHP::get_instance()->given($name, $value, $is_parsed);
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
    function when($name, $callback = null)
    {

        GivenPHP::get_instance()->when($name, $callback);
    }
}

if (!function_exists('then')) {
    /**
     * @see GivenPHP::then
     *
     * @param $callback
     *
     * @return void
     */
    function then($callback)
    {
        GivenPHP::get_instance()->then($callback);
    }
}
