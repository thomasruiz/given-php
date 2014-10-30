<?php

namespace GivenPHP;

use GivenPHP\Reporting\IReporter;

class GivenPHP
{

    const VERSION = '1.0.0';

    const EMPTY_VALUE = '__GIVEN_PHP_EV__';

    /**
     * The instance for Singleton pattern
     *
     * @var static $instance
     */
    private static $instance;

    /**
     * The suite that is currently running
     *
     * @var TestSuite $currentSuite
     */
    private $currentSuite;

    /**
     * All results from all the tests
     *
     * @var TestResult[] $testResults
     */
    private $testResults = [];

    /**
     * The reporter chose in the command line
     *
     * @var IReporter $reporter
     */
    private $reporter;

    /**
     * True if a test did not pass
     *
     * @var bool $hasError
     */
    private $hasError;

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
        $this->reporter->suiteStarted($this->currentSuite);
        $this->currentSuite->run();
        $this->reporter->suiteEnded($this->currentSuite);

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
     * Run a specific callback that must use assertions or return a boolean, whether the test succeed or not
     * It corresponds to the Assert part of the AAA pattern
     *
     * @param callable $callback
     *
     * @return TestResult
     */
    public function then($callback)
    {
        $testCase = new TestCase($callback);
        $this->reporter->testStarted($testCase);
        $result = $testCase->run($this->currentSuite);
        $this->reporter->testEnded($result);
        $this->testResults[] = $result;

        if ($result->isError()) {
            $this->hasError = true;
        }

        return $result;
    }

    /**
     * The tearDown() keyword
     * Add a callback to be run after each test in the context
     *
     * @param callable $callback
     *
     * @return void
     */
    public function tearDown($callback)
    {
        $this->currentSuite->addTearDownAction($callback);
    }

    /**
     * Setter for $reporter
     *
     * @param IReporter $reporter
     */
    public function setReporter(IReporter $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * Getter for $testResults
     *
     * @return TestResult[]
     */
    public function getTestResults()
    {
        return $this->testResults;
    }

    /**
     * Getter for $hasError
     *
     * @return boolean
     */
    public function hasError()
    {
        return $this->hasError;
    }
}
