<?php

namespace GivenPHP;

class GivenPHP
{

    const EMPTY_VALUE = '__GIVEN_PHP_EV__';

    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @var TestSuite $currentSuite
     */
    private $currentSuite;

    /**
     * @var TestResult[] $testResults
     */
    private $testResults = [];

    /**
     * Constructor
     */
    private function __construct()
    {
    }

    /**
     * Singleton pattern
     *
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    /**
     * The describe() keyword
     * Initialize a new test suite for a module/class
     * describe() should be used once per file
     *
     * @param string   $label
     * @param callable $callback
     *
     * @return TestSuite
     */
    public function describe($label, $callback)
    {
        $this->currentSuite = new TestSuite($label, $callback);
        $this->currentSuite->run();

        return $this->currentSuite;
    }

    /**
     * The context() keyword
     * Add a specific context to the test suite
     * context() can be nested and is used to isolate your tests
     *
     * @param string   $label
     * @param callable $callback
     *
     * @return mixed
     */
    public function context($label, $callback)
    {
        return $this->currentSuite->isolateContext($label, $callback);
    }

    /**
     * The given() keyword
     * Add a new value to be used in your tests
     * It corresponds to the Arrange part of the AAA pattern
     *
     * @param string   $name
     * @param callable $value
     *
     * @return mixed
     */
    public function given($name, $value)
    {
        return $this->currentSuite->addUncompiledValue($name, $value);
    }

    /**
     * The when() keyword
     * Add a new action to be executed before running the test
     * It corresponds to the Act part of the AAA pattern
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return mixed
     */
    public function when($name, $callback)
    {
        return $this->currentSuite->addAction($name, $callback);
    }

    /**
     * The then() keyword
     * Runs a specific callback that must use assertions or return a boolean, whether the test succeed or not
     * It corresponds to the Assert part of the AAA pattern
     *
     * @param callable $callback
     *
     * @return TestResult
     */
    public function then($callback)
    {
        $testCase            = new TestCase($callback);
        $result              = $testCase->run($this->currentSuite);
        $this->testResults[] = $result;

        return $result;
    }
}
