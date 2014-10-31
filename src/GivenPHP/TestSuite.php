<?php

namespace GivenPHP;

class TestSuite
{

    /**
     * The context used for the tests
     *
     * @var TestContext $currentContext
     */
    private $currentContext;

    /**
     * Dependancy Injection of the TestContext class
     *
     * @var string $contextClass
     */
    private $contextClass;

    /**
     * Constructor
     *
     * @param string   $label
     * @param callable $callback
     * @param string   $contextClass
     */
    public function __construct($label, $callback, $contextClass = 'GivenPHP\TestContext')
    {
        $this->contextClass   = $contextClass;
        $this->currentContext = is_string($contextClass) ? new $contextClass($label, $callback) : $contextClass;
    }

    /**
     * Add an isolated context to the suite
     *
     * @param string   $label
     * @param callable $callback
     *
     * @return mixed
     */
    public function isolateContext($label, $callback)
    {
        $savedContext = $this->currentContext;

        $this->currentContext =
            is_string($this->contextClass) ? new $this->contextClass($label, $callback) : $this->contextClass;
        $this->currentContext->addParentContext($savedContext);

        $rc = $this->currentContext->run($this);

        $this->currentContext = $savedContext;

        return $rc;
    }

    /**
     * Run the test suite
     *
     * @return mixed
     */
    public function run()
    {
        return $this->currentContext->run($this);
    }

    /**
     * @see TestContext::reset
     */
    public function reset()
    {
        $this->currentContext->reset();
    }

    /**
     * @see TestContext::executeActions
     * @return bool
     */
    public function executeActions()
    {
        $this->currentContext->executeActions();
    }

    /**
     * @see TestContext::addUncompiledValue
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function addUncompiledValue($name, $value)
    {
        return $this->currentContext->addUncompiledValue($name, $value);
    }

    /**
     * @see TestContext::addAction
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return mixed
     */
    public function addAction($name, $callback)
    {
        return $this->currentContext->addAction($name, $callback);
    }

    /**
     * @see TestContext::addTearDownAction
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public function addTearDownAction($callback)
    {
        return $this->currentContext->addTearDownAction($callback);
    }

    /**
     * @see TestContext::addSetUpAction
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public function addSetUpAction($callback)
    {
        return $this->currentContext->addSetUpAction($callback);
    }

    /**
     * @see TestContext::executeCallable
     *
     * @param callable $action
     *
     * @return mixed
     */
    public function executeCallback($action)
    {
        return $this->currentContext->executeCallback($action);
    }

    /**
     * @see TestContext::tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        $this->currentContext->tearDown();
    }

    /**
     * @see TestContext::setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->currentContext->setUp();
    }

    /**
     * Getter for $currentContext
     *
     * @return TestContext
     */
    public function getCurrentContext()
    {
        return $this->currentContext;
    }
}
