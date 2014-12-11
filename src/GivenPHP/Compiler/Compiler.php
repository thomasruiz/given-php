<?php namespace GivenPHP\Compiler;

use GivenPHP\EnhancedCallback\EnhancedCallback;
use GivenPHP\Suite\Context;
use GivenPHP\Value\RawValue;

class Compiler
{

    /**
     * Compile a value.
     *
     * @param RawValue $value
     * @param Context  $context
     *
     * @return mixed
     */
    public function compile(RawValue $value, Context $context)
    {
        $value = $value->getValue();

        if (is_callable($value)) {
            return $this->executeCallback($value, $context);
        }

        return $value;
    }

    /**
     * Execute a callback within EnhancedCallback.
     *
     * @param callable $callback
     * @param Context  $context
     *
     * @return mixed
     */
    public function executeCallback($callback, Context $context)
    {
        $cb = new EnhancedCallback($callback);

        return $cb->__invoke($context); // cannot call directly $cb($this) for testing purpose: unable to mock it
    }
}
