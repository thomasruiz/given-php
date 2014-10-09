<?php

use GivenPHP\EnhancedCallback;
use GivenPHP\Error;
use GivenPHP\Label;
use GivenPHP\Reporter\DefaultReporter;
use GivenPHP\Reporter\IReporter;
use GivenPHP\TestResult;
use GivenPHP\TestSuite;

require 'utils.php';

// @todo Handle PHP errors and exceptions

/**
 * Class GivenPHP
 */
class GivenPHP
{

    /**
     * The current version of the framework
     */
    const VERSION = '0.1.0';

    /**
     * The default value for given, when and then statements
     */
    const EMPTY_VALUE = 'GIVEN_PHP_EMPTY_VALUE';

    /**
     * Singleton
     *
     * @static
     * @var GivenPHP $instance
     */
    private static $instance = null;

    /**
     * The list of the test suites to run
     *
     * @var TestSuite[] $suites
     */
    private $suites = [];

    /**
     * The verbose labels of tests
     *
     * @var Label[] $labels
     */
    private $labels = [];

    /**
     * The current test suite running
     *
     * @var TestSuite $current_suite
     */
    private $current_suite = null;

    /**
     * The list of the errors of every test
     *
     * @var TestResult[] $errors
     */
    private $errors = [];

    /**
     * The list of the results of every test
     *
     * @var TestResult[] $results
     */
    private $results = [];

    /**
     * The reporter used for output
     *
     * @var IReporter $reporter
     */
    private $reporter;

    /**
     * The started test, changed in start() method
     *
     * @var boolean $isStarted
     */
    private $isStarted = false;

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->setReporter(new DefaultReporter());
        assert_options(ASSERT_ACTIVE, 1);
        assert_options(ASSERT_WARNING, 0);
        assert_options(ASSERT_QUIET_EVAL, 1);
        assert_options(ASSERT_CALLBACK, 'GivenPHP\Error::assertHandler');
    }

    /**
     * Destructor
     * Print the result when the script ends
     */
    public function __destruct()
    {
        $totalResults = count($this->results);
        $this->reporter->reportEnd($totalResults, $this->errors, $this->labels, $this->results);
    }

    /**
     * Singleton
     *
     * @return static
     */
    public static function get_instance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Sets the state to started
     *
     * @throws Exception
     */
    public function start()
    {
        if ($this->isStarted) {
            throw new Exception('The test is already started');
        }

        $this->isStarted = true;
        $this->reporter->reportStart(self::VERSION);
    }

    /**
     * Set the reporter to be used
     *
     * @param IReporter $reporter
     *
     * @throws Exception
     */
    public function setReporter(IReporter $reporter)
    {
        if ($this->isStarted) {
            throw new Exception('Unable to change the reporter when the test is already started');
        }

        $this->reporter = $reporter;
    }

    /**
     * The describe keyword
     * Initialize a new TestSuite
     * This should be used only once per test file
     *
     * @param string   $description
     * @param callback $callback
     *
     * @return void
     */
    public function describe($description, $callback)
    {
        $suite               = new TestSuite($description);
        $this->suites[]      = $suite;
        $this->current_suite = $suite;
        $callback();
    }

    /**
     * The context keyword
     * Isolates the tests ran in $callback
     *
     * @param string   $description
     * @param callback $callback
     *
     * @return void
     */
    public function context($description, $callback)
    {
        // Ensure that the base context is clean for further contexts
        $state = clone $this->current_suite;

        $this->current_suite->add_description($description);
        $callback();

        $this->current_suite = $state;
    }

    /**
     * The given keyword
     * Initialize a new value for the test
     *
     * @param string  $name
     * @param mixed   $value
     * @param boolean $is_parsed
     * @param string  $label
     *
     * @return void
     */
    public function given($name, $value, $is_parsed = false, $label = null)
    {
        $this->current_suite->add_value($name, $value, $is_parsed, $label);
    }

    /**
     * The when keyword
     * Add a callback to be run whenever a then is called
     *
     * @param string   $name
     * @param callback $callback
     * @param string   $label
     *
     * @return void
     */
    public function when($name, $callback = null, $label = null)
    {
        $this->current_suite->add_action($name, $callback, $label);
    }

    /**
     * The then keyword
     * Run the actual test
     * All given values needed will be parsed, and will execute every actions given by when
     * Will store the result of the test for further use
     *
     * @param callback $callback
     * @param string   $label
     *
     * @return void
     * @throws Exception
     */
    public function then($callback, $label)
    {
        $saved      = clone $this->current_suite;
        $errorFound = false;

        if ($callback instanceof Error) {
            $callback = function () use ($callback) {
                return $callback;
            };
        }

        try {
            $result = $this->current_suite->run($callback);
        } catch (Exception $e) {
            $result     = $this->errorHandling($e, $callback);
            $errorFound = true;
        }

        $this->current_suite = $saved;

        $testNumber      = count($this->results);
        $testDescription = $this->current_suite->description();
        $this->results[] = $result;

        if ($result->is_error() || $this->current_suite->expectsFailure() && $errorFound === false) {
            $this->errors[] = $result;
            $this->labels[] = $label;
            $this->reporter->reportFailure($testNumber, $testDescription);
        } else {
            $this->reporter->reportSuccess($testNumber, $testDescription);
        }
    }

    /**
     * @param Exception $e
     * @param callable  $callback
     *
     * @return TestResult
     */
    private function errorHandling(Exception $e, $callback)
    {
        if ($this->current_suite->expectsFailure($e)) {
            return new TestResult(true, $this->current_suite, new EnhancedCallback($callback));
        }

        return new TestResult(false, $this->current_suite, $this->current_suite->getLastCallback());
    }

    /**
     * The fails and failsWith keywords
     * These are used in case of expected failure from the tests
     *
     * @param Exception|bool $e
     *
     * @return Error
     */
    public function fails($e = true)
    {
        $this->current_suite->addExpectedFailure($e);

        return new Error();
    }
}
