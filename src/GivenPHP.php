<?php

use GivenPHP\TestResult;
use GivenPHP\TestSuite;

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
    private $suites = [ ];

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
    private $errors = [ ];

    /**
     * The list of the results of every tests
     *
     * @var TestResult[] $results
     */
    private $results = [ ];

    /**
     * Constructor
     */
    private function __construct()
    {
        echo 'GivenPHP v' . self::VERSION . PHP_EOL . PHP_EOL;
    }

    /**
     * Destructor
     * Print the result when the script ends
     */
    public function __destruct()
    {
        if (!empty($this->errors)) {
            foreach ($this->errors AS $i => $error) {
                $error->render($i + 1);
            }

            echo PHP_EOL . PHP_EOL . chr(27) . '[31m' . count($this->results) . ' examples, ' . count($this->errors) .
                 ' failures';

            echo PHP_EOL . PHP_EOL . chr(27) . '[0m' . 'Failed examples:';

            foreach ($this->errors AS $error) {
                $error->summary();
            }

            echo PHP_EOL;
        } else {
            echo PHP_EOL . PHP_EOL . chr(27) . '[32m' . count($this->results) . ' examples, 0 failures';
        }

        echo chr(27) . '[0m' . PHP_EOL;
    }

    /**
     * Singleton
     *
     * @return static
     */
    public static function get_instance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
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
     *
     * @return void
     */
    public function given($name, $value, $is_parsed = false)
    {
        $this->current_suite->add_value($name, $value, $is_parsed);
    }

    /**
     * The when keyword
     * Add a callback to be run whenever a then is called
     *
     * @param $callback
     *
     * @return void
     */
    public function when($callback)
    {
        $this->current_suite->add_action($callback);
    }

    /**
     * The then keyword
     * Run the actual test
     * All given values needed will be parsed, and will execute every actions given by when
     * Will store the result of the test for further use
     *
     * @param $callback
     *
     * @return void
     * @throws Exception
     */
    public function then($callback)
    {
        $saved               = clone $this->current_suite;
        $result              = $this->current_suite->run($callback);
        $this->current_suite = $saved;

        $this->results[] = $result;
        if ($result->is_error()) {
            $this->errors[] = $result;
            echo chr(27) . '[31mF' . chr(27) . '[0m';
        } else {
            echo '.';
        }
    }
}
