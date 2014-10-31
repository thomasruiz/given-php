<?php

namespace GivenPHP;

class TestContext
{

    /**
     * The values not yet compiled corresponding to the given() statements
     *
     * @var array $uncompiledValues
     */
    private $uncompiledValues = [];

    /**
     * The values that have been compiled
     *
     * @var array $compiledValues
     */
    private $compiledValues = [];

    /**
     * The actions corresponding to the when() statements
     *
     * @var array
     */
    private $actions = [];

    /**
     * The callback corresponding to the context() statement (and the describe statement as well)
     *
     * @var callable $callback
     */
    private $callback;

    /**
     * An explicative label of the context
     *
     * @var string $label
     */
    private $label;

    /**
     * The last callback that have been run
     *
     * @var EnhancedCallback $currentCallback
     */
    private $currentCallback;

    /**
     * The actions to run after each test
     *
     * @var callable[] $tearDownActions
     */
    private $tearDownActions = [];

    /**
     * The actions to run before each test
     *
     * @var callable[] $setUpActions
     */
    private $setUpActions = [];

    /**
     * Constructor.
     *
     * @param string   $label
     * @param callable $callback
     */
    public function __construct($label, $callback)
    {
        $this->label    = $label;
        $this->callback = $callback;
    }

    /**
     * Run the context callback that will contain the given(), when() and then() statements
     *
     * @return mixed
     */
    public function run()
    {
        $cb = $this->callback;

        return $cb();
    }

    /**
     * Compile the value if needed, and return a reference to it
     *
     * @param string $name
     *
     * @return mixed
     */
    public function &getValue($name)
    {
        if (!isset($this->compiledValues[$name])) {
            $this->compiledValues[$name] = $this->compileValue($this->uncompiledValues[$name]);
        }

        return $this->compiledValues[$name];
    }

    /**
     * Execute a callback within EnhancedCallback
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public function executeCallback($callback)
    {
        $cb                    = new EnhancedCallback($callback);
        $this->currentCallback = $cb;

        return $cb($this);
    }

    /**
     * Used for nested context() statements
     *
     * @param TestContext $context
     */
    public function addParentContext(TestContext $context)
    {
        $this->compiledValues   = $context->compiledValues;
        $this->uncompiledValues = $context->uncompiledValues;
        $this->actions          = $context->actions;
        $this->label            = $context->getLabel() . ' ' . $this->label;
        $this->tearDownActions  = $context->tearDownActions;
        $this->setUpActions     = $context->setUpActions;
    }

    /**
     * Execute all when() statements in context
     *
     * @return void
     */
    public function executeActions()
    {
        foreach ($this->actions as $name => $action) {
            $this->compiledValues[$name] = $this->executeCallback($action);
        }
    }

    /**
     * Execute all tearDown() callbacks
     *
     * @return void
     */
    public function tearDown()
    {
        foreach ($this->tearDownActions as $action) {
            $this->executeCallback($action);
        }
    }

    /**
     * Execute all setUp() callbacks
     *
     * @return void
     */
    public function setUp()
    {
        foreach ($this->setUpActions as $action) {
            $this->executeCallback($action);
        }
    }

    /**
     * Add a value to be compiled, used by given() statement
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function addUncompiledValue($name, $value)
    {
        return $this->uncompiledValues[$name] = $value;
    }

    /**
     * Add an action to be ran, used by when() statement
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return mixed
     */
    public function addAction($name, $callback)
    {
        if ($name === null) {
            return $this->actions[] = $callback;
        }

        return $this->actions[$name] = $callback;
    }

    /**
     * Add an action to be ran after each test
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public function addTearDownAction($callback)
    {
        return $this->tearDownActions[] = $callback;
    }

    /**
     * Add an action to be ran before each test
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public function addSetUpAction($callback)
    {
        return $this->setUpActions[] = $callback;
    }

    /**
     * Reset the context
     */
    public function reset()
    {
        $this->compiledValues = [];
    }

    /**
     * Getter of $label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Getter for $currentCallback
     *
     * @return EnhancedCallback
     */
    public function getCurrentCallback()
    {
        return $this->currentCallback;
    }

    /**
     * Compile the given value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function compileValue($value)
    {
        if (is_callable($value)) {
            return $this->executeCallback($value);
        }

        return $value;
    }
}
