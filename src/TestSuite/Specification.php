<?php namespace GivenPHP\TestSuite;

use Countable;

class Specification implements Countable
{

    /**
     * @var Suite
     */
    private $suite;

    /**
     * @var Context
     */
    private $currentContext;

    /**
     * @var Context[]
     */
    private $contexts = [ ];

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $constructorParameters;

    /**
     * Construct a new Specification object
     *
     * @param string  $classUnderSpec
     * @param array   $constructorParameters
     * @param Context $context
     */
    public function __construct($classUnderSpec, array $constructorParameters, Context $context)
    {
        $this->title                 = $classUnderSpec;
        $this->contexts[]            = $this->currentContext = $context;
        $this->constructorParameters = $constructorParameters;
    }

    /**
     * @param Context $context
     */
    public function addContext(Context $context)
    {
        $parentContext = $this->currentContext;
        $this->contexts[] = $context;

        $this->currentContext = $context;
        $context->run();
        $this->currentContext = $parentContext;
    }

    public function run()
    {
        $this->currentContext->run();
    }

    /**
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, array $arguments)
    {
        call_user_func_array([ $this->currentContext, $name ], $arguments);
    }

    /**
     * @return number
     */
    public function count()
    {
        return array_sum(array_map('count', $this->contexts));
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getConstructorParameters()
    {
        return $this->constructorParameters;
    }

    /**
     * @return Context
     */
    public function getCurrentContext()
    {
        return $this->currentContext;
    }

    /**
     * @return Context[]
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @return Suite
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * @param Suite $suite
     */
    public function setSuite($suite)
    {
        $this->suite = $suite;
    }
}