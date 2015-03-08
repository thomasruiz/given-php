<?php namespace GivenPHP\TestSuite;

use Countable;

class Context implements Countable
{

    /**
     * @var string
     */
    private $context;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var callable[]
     */
    private $examples = [ ];

    /**
     * @var callable[]
     */
    private $values = [ ];

    /**
     * @var callable[]
     */
    private $modifiers = [ ];

    /**
     * @var callable[]
     */
    private $actions = [ ];

    /**
     * @var callable[]
     */
    private $letCallbacks = [ ];

    /**
     * @var array
     */
    private $compiledValues = [ ];

    /**
     * Construct a new Context object
     *
     * @param string   $context
     * @param callable $callback
     * @param Context  $parent
     */
    public function __construct($context, callable $callback, Context $parent = null)
    {
        $this->context  = $context;
        $this->callback = $callback;

        if ($parent !== null) {
            $this->context        = $parent->getContext() . ' ' . $context;
            $this->values         = $parent->getValues();
            $this->modifiers      = $parent->getModifiers();
            $this->actions        = $parent->getActions();
            $this->compiledValues = $parent->getCompiledValues();
        }
    }

    /**
     * Run the callback of the context (spec definition)
     *
     * @return mixed
     */
    public function run()
    {
        $callback = $this->callback;

        return $callback();
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function addCompiledValue($name, $value)
    {
        return $this->compiledValues[ $name ] = $value;
    }

    /**
     * @param string   $name
     * @param callable $callback
     */
    public function addValue($name, callable $callback)
    {
        $this->values[ $name ] = $callback;
    }

    /**
     * @param callable $callback
     */
    public function addModifier(callable $callback)
    {
        $this->modifiers[] = $callback;
    }

    /**
     * @param string   $name
     * @param callable $callback
     */
    public function addActionWithResult($name, callable $callback)
    {
        $this->actions[ $name ] = $callback;
    }

    /**
     * @param callable $callback
     */
    public function addActionWithoutResult(callable $callback)
    {
        $this->actions[] = $callback;
    }

    /**
     * @param callable $callback
     */
    public function addLetCallback(callable $callback)
    {
        $this->letCallbacks[] = $callback;
    }

    /**
     * @param callable $example
     */
    public function addExample(callable $example)
    {
        $this->examples[] = $example;
    }

    /**
     * @return number
     */
    public function count()
    {
        return array_sum($this->examples);
    }

    /**
     * @return callable[]
     */
    public function getExamples()
    {
        return $this->examples;
    }

    /**
     * @return callable[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return callable[]
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * @return callable[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return callable[]
     */
    public function getLetCallbacks()
    {
        return $this->letCallbacks;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasCompiledValue($name)
    {
        return isset( $this->compiledValues[ $name ] );
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getCompiledValue($name)
    {
        return $this->compiledValues[ $name ];
    }

    /**
     * @return array
     */
    public function getCompiledValues()
    {
        return $this->compiledValues;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasValue($name)
    {
        return isset( $this->values[ $name ] );
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getValue($name)
    {
        return $this->values[ $name ];
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }
}