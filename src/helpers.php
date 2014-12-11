<?php

use GivenPHP\Suite\Suite;
use GivenPHP\Value\EmptyValue;

if (!function_exists('describe')) {
    /**
     * Describe a new Suite.
     *
     * @param string   $label
     * @param callable $callback
     *
     * @return Suite
     */
    function describe($label, $callback)
    {
        $suite = new Suite($label, $callback);

        return current_suite($suite);
    }
}

if (!function_exists('context')) {
    /**
     * Isolate a context within the suite.
     *
     * @param string   $label
     * @param callable $callback
     *
     * @return void
     */
    function context($label, $callback)
    {
        current_suite()->isolateContext($label, $callback);
    }
}

if (!function_exists('given')) {
    /**
     * Give a new value to the context.
     *
     * @param string $label
     * @param string $name
     * @param mixed  $value
     *
     * @return void
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
     * Add a new action to the context.
     *
     * @param string   $label
     * @param string   $name
     * @param callable $action
     *
     * @return void
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
     * Register a new test to the context.
     *
     * @param string   $label
     * @param callable $test
     *
     * @return void
     */
    function then($label, $test = EmptyValue::class)
    {
        if ($test === EmptyValue::class) {
            $test = $label;
        }

        current_suite()->getCurrentContext()->addTest($label, $test);
    }
}

if (!function_exists('current_suite')) {
    /**
     * Get/Set the current suite for above functions.
     *
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
