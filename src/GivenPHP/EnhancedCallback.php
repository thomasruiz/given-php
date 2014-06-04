<?php

namespace GivenPHP;

use ReflectionFunction;
use SplFileObject;

class EnhancedCallback
{

    private $callback;

    private $reflection;

    public function __construct($callback)
    {
        $this->callback   = $callback;
        $this->reflection = new ReflectionFunction($callback);
    }

    /**
     * @param TestSuite $context
     *
     * @return mixed
     */
    public function __invoke($context)
    {
        $call_parameters = $this->parameters($context);

        return call_user_func_array($this->callback, $call_parameters);
    }

    /**
     * @param TestSuite $context
     *
     * @return array
     */
    public function parameters($context)
    {
        $parameters      = $this->reflection->getParameters();
        $call_parameters = [ ];
        foreach ($parameters as $i => $param) {
            $call_parameters[$i] = &$context->get_value($param->getName());
            $call_parameters[$param->getName()] = &$call_parameters[$i];
        }

        return $call_parameters;
    }

    /**
     * @return string
     */
    public function code()
    {
        $file = new SplFileObject($this->reflection->getFileName());
        $file->seek($this->reflection->getStartLine());

        $code = '';
        while ($file->key() <$this->reflection->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }

        $begin = strpos($code, 'function');
        $end   = strrpos($code, '}');
        $code  = substr($code, $begin, $end - $begin);

        return trim(str_replace('return', '', $code));
    }

    public function line()
    {
        return $this->reflection->getEndLine();
    }

    public function file()
    {
        return $this->reflection->getFileName();
    }
}
