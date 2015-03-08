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
        return GivenPHP::addLetCallback($callback);
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
     * @param mixed $constructorParameters,...
     * @param mixed $constructorParameters,... unlimited OPTIONAL
     *
     * @return array
     */
    function with($constructorParameters)
    {
        return func_num_args() > 1 ? func_get_args()
            : (is_array($constructorParameters) ? $constructorParameters : [ $constructorParameters ]);
    }
}

if (!function_exists('context')) {
    /**
     * Describe a new specification
     *
     * @param string   $context
     * @param callable $callback
     *
     * @return Suite
     */
    function context($context, callable $callback)
    {
        return GivenPHP::__callStatic('addContext', [ $context, $callback ]);
    }
}

if (!function_exists('given')) {
    /**
     * Add a new value to the spec
     *
     * @param string|callable $nameOrCallback
     * @param callable        $callback
     *
     * @return Suite
     */
    function given($nameOrCallback, callable $callback = null)
    {
        if ($callback === null) {
            return GivenPHP::addModifier($nameOrCallback);
        }

        return GivenPHP::addValue($nameOrCallback, $callback);
    }
}

if (!function_exists('when')) {
    /**
     * Add a new action to the spec
     *
     * @param string|callable $nameOrCallback
     * @param callable        $callback
     *
     * @return Suite
     */
    function when($nameOrCallback, callable $callback = null)
    {
        if ($callback === null) {
            return GivenPHP::addActionWithoutResult($nameOrCallback);
        }

        return GivenPHP::addActionWithResult($nameOrCallback, $callback);
    }
}

if (!function_exists('then')) {
    /**
     * Add a new example to the spec
     *
     * @param callable $callback
     *
     * @return Suite
     */
    function then(callable $callback)
    {
        return GivenPHP::addExample($callback);
    }
}