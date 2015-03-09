<?php

use GivenPHP\GivenPHP;
use GivenPHP\TestSuite\Suite;

if (!function_exists('let')) {
    /**
     * Add a new action to be run before object construction
     *
     * @param $callback
     */
    function let($callback)
    {
        GivenPHP::addLetCallback($callback);
    }
}

if (!function_exists('describe')) {
    /**
     * Describe a new specification
     *
     * @param string         $classUnderSpec
     * @param array|callable $constructorParametersOrCallback
     * @param callable       $callback
     *
     * @return Suite
     */
    function describe($classUnderSpec, $constructorParametersOrCallback, callable $callback = null)
    {
        if ($callback === null) {
            $callback                        = $constructorParametersOrCallback;
            $constructorParametersOrCallback = [ ];
        }

        return GivenPHP::__callStatic('describe', [ $classUnderSpec, $constructorParametersOrCallback, $callback ]);
    }
}

if (!function_exists('with')) {
    /**
     * Describe a new specification
     *
     * @param mixed $constructorParameters
     * @param mixed $_
     *
     * @return array
     */
    function with($constructorParameters, $_ = null)
    {
        return func_num_args() > 1 ? func_get_args()
            : ( is_array($constructorParameters) ? $constructorParameters : [ $constructorParameters ] );
    }
}

if (!function_exists('context')) {
    /**
     * Describe a new specification
     *
     * @param string   $context
     * @param callable $callback
     */
    function context($context, callable $callback)
    {
        GivenPHP::__callStatic('addContext', [ $context, $callback ]);
    }
}

if (!function_exists('given')) {
    /**
     * Add a new value to the spec
     *
     * @param string|callable $nameOrCallback
     * @param callable        $callback
     */
    function given($nameOrCallback, callable $callback = null)
    {
        if ($callback === null) {
            GivenPHP::addModifier($nameOrCallback);
        } else {
            GivenPHP::addValue($nameOrCallback, $callback);
        }
    }
}

if (!function_exists('when')) {
    /**
     * Add a new action to the spec
     *
     * @param string|callable $nameOrCallback
     * @param callable        $callback
     */
    function when($nameOrCallback, callable $callback = null)
    {
        if ($callback === null) {
            GivenPHP::addActionWithoutResult($nameOrCallback);
        } else {
            GivenPHP::addActionWithResult($nameOrCallback, $callback);
        }
    }
}

if (!function_exists('then')) {
    /**
     * Add a new example to the spec
     *
     * @param callable $callback
     */
    function then(callable $callback)
    {
        GivenPHP::addExample($callback);
    }
}