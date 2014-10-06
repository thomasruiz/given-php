<?php
namespace GivenPHP;

use ReflectionFunction;
use SplFileObject;

/**
 * Class EnhancedCallback
 *
 * @package GivenPHP
 */
class EnhancedCallback
{

    /**
     * The actual callback
     *
     * @var callable $callback
     */
    private $callback;

    /**
     * The reflection about the callback
     *
     * @var ReflectionFunction $reflection
     */
    private $reflection;

    /**
     * Constructor
     *
     * @param callable $callback
     */
    public function __construct($callback)
    {
        $this->callback   = $callback;
        $this->reflection = new ReflectionFunction($callback);
    }

    /**
     * Run the callback
     *
     * @param bool|TestSuite $context
     * @param array                    $parameters
     *
     * @return mixed
     */
    public function __invoke($context = false, $parameters = [ ])
    {
        $call_parameters = $context ? $this->parameters($context, false) : $parameters;

        return call_user_func_array($this->callback, $call_parameters);
    }

    /**
     * Retrieve the parameters from the context according to their name in the callback function
     *
     * @param TestSuite $context
     * @param bool      $with_names
     *
     * @return array
     */
    public function parameters($context, $with_names = true)
    {
        $parameters      = $this->reflection->getParameters();
        $call_parameters = [ ];
        foreach ($parameters as $i => $param) {
            $call_parameters[$i] = & $context->get_value($param->getName());

            if ($with_names) {
                $call_parameters[$param->getName()] = & $call_parameters[$i];
            }
        }
        return $call_parameters;
    }

    /**
     * Retrieve the code of the callback
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
        $code  = substr($code, $begin, $end - $begin);

        return trim(str_replace('return', '', $code));
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
