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
     * @param $label
     * @param $name
     * @param $value
     *
     * @return void
     */
    function given($label, $name, $value = GivenPHP::EMPTY_VALUE)
    {
        if ($value === GivenPHP::EMPTY_VALUE) {
            $value = $name;
            $name  = $label;
            $label = null;
        }

        GivenPHP::get_instance()->given($name, $value, false, $label);
    }
}

if (!function_exists('when')) {
    /**
     * @see GivenPHP::when
     *
     * @param $label
     * @param $name
     * @param $callback
     *
     * @return void
     */
    function when($label, $name = null, $callback = GivenPHP::EMPTY_VALUE)
    {
        if ($callback === GivenPHP::EMPTY_VALUE) {
            $callback = $name;
            $name     = $label;
            $label    = null;
        }

        GivenPHP::get_instance()->when($name, $callback, $label);
    }
}

if (!function_exists('then')) {
    /**
     * @see GivenPHP::then
     *
     * @param string $label
     * @param string $callback
     *
     * @return void
     */
    function then($label, $callback = GivenPHP::EMPTY_VALUE)
    {
        if ($callback === GivenPHP::EMPTY_VALUE) {
            $callback = $label;
            $label    = null;
        }

        GivenPHP::get_instance()->then($callback, $label);
    }
}

if (!function_exists('fails')) {
    /**
     * @see GivenPHP::fails
     *
     * @return \GivenPHP\Error
     */
    function fails()
    {
        return GivenPHP::get_instance()->fails();
    }

    /**
     * @see GivenPHP::fails
     *
     * @param $e
     *
     * @return \GivenPHP\Error
     */
    function failsWith($e) {
        return GivenPHP::get_instance()->fails($e);
    }
}
