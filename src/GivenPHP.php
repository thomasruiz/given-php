<?php

use GivenPHP\TestResult;
use GivenPHP\TestSuite;
use GivenPHP\IReporter;

require 'utils.php';

// @todo Add a Formatter
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
     * The defualt value for given, when and then statements
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
     * @var array
     */
    private $labels = [];

    /**
     * The current test suite running
     *
     * @var TestSuite $current_suite
     */
    private $current_suite = null;

    /**
     * The list of the errors of every tests
     *
     * @var TestResult[] $errors
     */
    private $errors = [];

    /**
     * The list of the results of every tests
     *
     * @var TestResult[] $results
     */
    private $results = [];

    /**
     * Constructor
     */
    private function __construct(IReporter $reporter)
    {
        $this->reporter = $reporter;
        $this->reporter->reportStart(self::VERSION);
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
    public static function get_instance(IReporter $reporter = null)
    {
        if (static::$instance === null) {

            //on singleton creation, pass a reporter class
            $message = 'GivenPHP singleton must be instantiated with a reporter';
            if ($reporter === null) {
                throw new Exception($message);
            }
            static::$instance = new static($reporter);
        }

        return static::$instance;
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
     * @param $description
     * @param $callback
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
     * @param $name
     * @param $value
     * @param $is_parsed
     * @param $label
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
     * @param $name
     * @param $callback
     * @param $label
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
     * @param $callback
     * @param $label
     *
     * @return void
     * @throws Exception
     */
    public function then($callback, $label)
    {
        $saved               = clone $this->current_suite;
        $result              = $this->current_suite->run($callback);
        $this->current_suite = $saved;

        $this->results[] = $result;
        if ($result->is_error()) {
            $this->errors[] = $result;
            $this->labels[] = $label;
            $testNumber      = count($this->results);
            $testDescription = $this->current_suite->description();
            $this->reporter->reportFailure($testNumber, $testDescription);
        } else {
            $testNumber      = count($this->results);
            $testDescription = $this->current_suite->description();
            $this->reporter->reportSuccess($testNumber, $testDescription);
        }
    }
}
