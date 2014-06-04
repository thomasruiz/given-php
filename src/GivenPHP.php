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

    const VERSION = '0.1.0';

    /**
     * @var static $instance
     */
    private static $instance = null;

    /**
     * @var TestSuite[] $suites
     */
    private $suites;

    /**
     * @var TestSuite $current_suite
     */
    private $current_suite;

    /**
     * @var TestResult[] $errors
     */
    private $errors;

    /**
     * @var TestResult[] $results
     */
    private $results;

    public function __construct()
    {
        $this->suites        = [ ];
        $this->current_suite = null;
        $this->errors        = [ ];
        $this->results       = [ ];

        echo 'GivenPHP v' . self::VERSION . PHP_EOL . PHP_EOL;
    }

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
     * @param string   $description
     * @param callback $callback
     */
    public function describe($description, $callback)
    {
        $suite               = new TestSuite($description);
        $this->suites[]      = $suite;
        $this->current_suite = $suite;
        $callback();
    }

    public function context($description, $callback)
    {
        // Ensure that the base context is clean for further contexts
        $state = clone $this->current_suite;

        $this->current_suite->add_description($description);
        $callback();

        $this->current_suite = $state;
    }

    public function given($name, $value)
    {
        $this->current_suite->add_value($name, $value);
    }

    public function when($callback)
    {
        $this->current_suite->add_action($callback);
    }

    public function then($callback)
    {
        $result = $this->current_suite->run($callback);

        $this->results[] = $result;
        if ($result->is_error()) {
            $this->errors[] = $result;
            echo chr(27) . '[31mF' . chr(27) . '[0m';
        } else {
            echo '.';
        }
    }
}
