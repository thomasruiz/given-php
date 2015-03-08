<?php namespace GivenPHP\Runners;

use GivenPHP\TestSuite\Context;
use Prophecy\Prophet;
use ReflectionFunction;

class FunctionRunner
{

    /**
     * @param callable $function
     * @param Context  $context
     * @param Prophet  $prophet
     *
     * @return mixed
     */
    public function run(callable $function, Context $context, Prophet $prophet)
    {
        $parameters = $this->buildParameters($function, $context, $prophet);

        $saveErrorHandler = set_error_handler([ $this, 'handleError' ], E_ALL ^ E_STRICT);
        try {
            return call_user_func_array($function, $parameters);
        } finally {
            set_error_handler($saveErrorHandler);
        }
    }

    /**
     * @param callable $function
     * @param Context  $context
     * @param Prophet  $prophet
     *
     * @return array
     */
    public function buildParameters(callable $function, Context $context, Prophet $prophet)
    {
        $functionReflection = new ReflectionFunction($function);

        $parameters = [ ];

        foreach ($functionReflection->getParameters() as $parameter) {
            $paramName = $parameter->getName();

            if ($context->hasCompiledValue($paramName)) {
                $parameters[] = $context->getCompiledValue($paramName);
            } else {
                if ($context->hasValue($paramName)) {
                    $value = $this->run($context->getValue($paramName), $context, $prophet);
                } else {
                    $value = $prophet->prophesize($parameter->getClass()->getName());
                }
                $parameters[] = $context->addCompiledValue($paramName, $value);
            }
        }

        return $parameters;
    }

    /**
     * @param $level
     * @param $message
     *
     * @return bool
     */
    public function handleError($level, $message)
    {
        $regex = '/^Argument (\d)+ passed to ([\w\\\]+){closure}\(\) must (?:be an instance of|implement interface) '
                 . '([\w\\\]+),(?: instance of)? ([\w\\\]+) given/';

        if (E_RECOVERABLE_ERROR === $level && preg_match($regex, $message, $matches)) {
            return true;
        }

        return false;
    }
}