<?php
namespace GivenPHP;

use ReflectionFunction;
use SplFileObject;

class EnhancedCallback
{

    /**
     * The actual encapsulated callback
     *
     * @var callable $callback
     */
    private $callback;

    /**
     * The reflection function corresponding to the callback
     *
     * @var ReflectionFunction $reflection
     */
    private $reflection;

    /**
     * Constructor
     *
     * @param $callback
     */
    public function __construct($callback)
    {
        $this->callback   = $callback;
        $this->reflection = new ReflectionFunction($callback);
    }

    /**
     * Invoke the callback with real parameters
     *
     * @param bool|TestContext $context
     * @param array            $parameters
     *
     * @return mixed
     */
    public function __invoke($context = false, $parameters = [])
    {
        $call_parameters = $context ? $this->parameters($context, false) : $parameters;

        return call_user_func_array($this->callback, $call_parameters);
    }

    /**
     * Retrieves the parameters of the callback
     *
     * @param TestContext $context
     * @param bool        $with_names
     *
     * @return array
     */
    public function parameters($context, $with_names = true)
    {
        $parameters      = $this->reflection->getParameters();
        $call_parameters = [];
        foreach ($parameters as $i => $param) {
            $call_parameters[$i] = &$context->getValue($param->getName());

            if ($with_names) {
                $call_parameters[$param->getName()] = &$call_parameters[$i];
            }
        }

        return $call_parameters;
    }

    /**
     * Retrieve the code of the function
     *
     * @return string
     */
    public function code()
    {
        $file = new SplFileObject($this->reflection->getFileName());
        $file->seek($this->reflection->getStartLine());

        $code = '';
        while ($file->key() < $this->reflection->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }

        $begin = strpos($code, 'function');
        $end   = strrpos($code, '}');
        $code  = trim(substr($code, $begin, $end - $begin));

        $codeLines = explode("\n", $code);

        if (count($codeLines) == 1) {
            return trim(str_replace('return', '', $codeLines[0]));
        }

        foreach ($codeLines as $i => $line) {
            $codeLines[$i] = trim($line);
        }

        return trim(implode(' ', $codeLines));
    }

    /**
     * Retrieve the line of the function
     *
     * @return int
     */
    public function line()
    {
        return $this->reflection->getEndLine();
    }

    /**
     * Retrieve the file of the function
     *
     * @return string
     */
    public function file()
    {
        return $this->reflection->getFileName();
    }
}
