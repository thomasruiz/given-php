<?php

namespace GivenPHP;

use GivenPHP\Expectation\Failure;
use GivenPHP\Reporting\IReporter;
use GivenPHP\Reporting\NullReporter;

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
     * True if a test is running
     *
     * @var bool $runningTest
     */
    private $runningTest;

    /**
     * Dependency Injection of the TestSuite class
     *
     * @var string|TestSuite
     */
    private $testSuiteClass;

    /**
     * Dependency Injection of the TestCase class
     *
     * @var string|TestCase
     */
    private $testCaseClass;

    /**
     * Dependency Injection of the Failure class
     *
     * @var string|Failure
     */
    private $failureClass;

    /**
     * Constructor
     *
     * @param string|TestSuite $testSuiteClass
     * @param string|TestCase  $testCaseClass
     * @param string|Failure   $failureClass
     */
    public function __construct($testSuiteClass = 'GivenPHP\TestSuite', $testCaseClass = 'GivenPHP\TestCase',
                                $failureClass = 'GivenPHP\Expectation\Failure')
    {
        $this->testSuiteClass = $testSuiteClass;
        $this->testCaseClass  = $testCaseClass;
        $this->failureClass   = $failureClass;
        $this->reporter       = new NullReporter();
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
        $this->currentSuite =
            is_string($this->testSuiteClass) ? new $this->testSuiteClass($label, $callback) : $this->testSuiteClass;
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
        if (!$this->currentSuite) {
            $this->statementNotInDescribe('context');
        }

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
        if (!$this->currentSuite) {
            $this->statementNotInDescribe('given');
        }

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
        if (!$this->currentSuite) {
            $this->statementNotInDescribe('when');
        }

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
        if (!$this->currentSuite) {
            $this->statementNotInDescribe('then');
        }

        $this->prepareForTestRun();
        $testCase = is_string($this->testCaseClass) ? new $this->testCaseClass($callback) : $this->testCaseClass;
        $this->reporter->testStarted($testCase);
        $result = $testCase->run($this->currentSuite);
        $this->reporter->testEnded($result);
        $this->closeTestRun();
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
        if (!$this->currentSuite) {
            $this->statementNotInDescribe('tearDown');
        }

        $this->currentSuite->addTearDownAction($callback);
    }

    /**
     * The setUp() keyword
     * Add a callback to be run before each test in the context
     *
     * @param callable $callback
     *
     * @return void
     */
    public function setUp($callback)
    {
        if (!$this->currentSuite) {
            $this->statementNotInDescribe('setUp');
        }

        $this->currentSuite->addSetUpAction($callback);
    }

    /**
     * The fails() keyword
     * Specifies that a test is expected to fail
     *
     * @param string $expectedFailure
     *
     * @return Failure
     */
    public function fails($expectedFailure = null)
    {
        if (!$this->currentSuite) {
            $this->statementNotInDescribe('fails');
        }

        return is_string($this->failureClass) ? new $this->failureClass($expectedFailure) : $this->failureClass;
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

    /**
     * Describe must be the first keyword to initialize suites
     *
     * @param string $keyword
     */
    private function statementNotInDescribe($keyword)
    {
        throw new \BadFunctionCallException("$keyword must be within a describe statement");
    }

    /**
     * Called before running a test case
     */
    private function prepareForTestRun()
    {
        if ($this->runningTest) {
            throw new \BadFunctionCallException('Then() is not allowed in given() or when() statements');
        }

        $this->runningTest = true;
    }

    /**
     * Called when a test case is finished
     */
    private function closeTestRun()
    {
        $this->runningTest = false;
    }
}
