<?php

use GivenPHP\Suite\Suite;
use GivenPHP\Value\EmptyValue;

if (!function_exists('current_suite')) {
    /**
     * @param Suite $suite
     *
     * @return Suite
     * @throws InvalidArgumentException
     */
    function current_suite($suite = null)
    {
        static $_suite = null;

        if ($_suite === null) {
            if ($suite instanceof Suite) {
                $_suite = $suite;
            } else {
                throw new InvalidArgumentException('$suite should be a Suite.');
            }
        }

        return $_suite;
    }
}

if (!function_exists('describe')) {
    /**
     * @param $label
     * @param $callback
     *
     * @return Suite
     */
    function describe($label, $callback)
    {
        $suite = new Suite($label, $callback);

        return current_suite($suite);
    }
}

if (!function_exists('given')) {
    /**
     * @param $label
     * @param $name
     * @param $value
     */
    function given($label, $name, $value = EmptyValue::class)
    {
        if ($value === EmptyValue::class) {
            $value = $name;
            $name  = $label;
        }

        current_suite()->getCurrentContext()->addValue($label, $name, $value);
    }
}

if (!function_exists('when')) {
    /**
     * @param $label
     * @param $name
     * @param $action
     */
    function when($label, $name = EmptyValue::class, $action = EmptyValue::class)
    {
        if ($action === EmptyValue::class) {
            if ($name === EmptyValue::class) {
                $action = $label;
            } else {
                $action = $name;
                $name   = strpos($label, ' ') !== -1 ? $label : EmptyValue::class;
            }
        }

        current_suite()->getCurrentContext()->addAction($label, $name, $action);
    }
}

if (!function_exists('then')) {
    /**
     * @param $label
     * @param $test
     */
    function then($label, $test = EmptyValue::class)
    {
        if ($test === EmptyValue::class) {
            $test = $label;
        }

        current_suite()->getCurrentContext()->addTest($label, $test);
    }
}
