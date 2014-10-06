<?php

namespace GivenPHP;

use Closure;
use Exception;

/**
 * Class TestSuite
 *
 * @package GivenPHP
 * @author  Thomas Ruiz <contact@thomasruiz.eu>
 */
class TestSuite
{

    /**
     * The complete description of the context
     *
     * @var string
     */
    private $description = null;

    /**
     * The list of the data added by the 'given' function
     *
     * @var array
     */
    private $data = [];

    /**
     * The list of the functions to execute added by the 'when' function
     *
     * @var callable[]
     */
    private $actions = [];

    /**
     * The verbose labels of values and actions
     *
     * @var array
     */
    private $labels = [];

    /**
     * This is to prevent the use of 'then' in a 'when', causing an infinite loop
     *
     * @var bool
     */
    private $executing_when = false;

    /**
     * The list of the real data
     *
     * @var array
     */
    private $parsed_data = [];

    /**
     * @var null
     */
    private $expectedFailure = null;

    /**
     * The last callback executed
     *
     * @var Callback $current_callback
     */
    private $current_callback;

    /**
     * Constructor
     *
     * @param string $description
     */
    public function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * Run the test suite.
     * It's important to note that when this function is called, the suite is isolated from
     *  all other 'then' statements
     *
     * @param callable $callback
     *
     * @return TestResult
     * @throws Exception
     */
    public function run($callback)
    {
        if ($this->executing_when) {
            throw new Exception('Unexpected then() in when()');
        }

        $this->executing_when = true;
        $this->execute_actions();
        $this->executing_when = false;
        $result               = $this->execute_callback($callback);
        return new TestResult($result, $this, $this->current_callback);
    }

    /**
     * Run every actions registered by when for this test
     *
     * @return void
     */
    private function execute_actions()
    {
        foreach ($this->actions AS $key => $action) {
            $result = $this->execute_callback($action);
            if (is_string($key)) {
                $this->parsed_data[$key] = $result;
            }
        }
    }

    /**
     * Execute a callback
     * Will parse the parameters from given statements
     *
     * @param callable $action
     *
     * @return mixed
     */
    private function execute_callback($action)
    {
        $callback               = new EnhancedCallback($action);
        $this->current_callback = $callback;
        return $callback($this);
    }

    /**
     * Add a better description to the current test (used in context)
     *
     * @param string $description
     *
     * @return void
     */
    public function add_description($description)
    {
        $this->description .= ' ' . $description;
    }

    /**
     * Add a value to be parsed (used in given)
     *
     * @param string  $name
     * @param mixed   $value
     * @param boolean $is_parsed
     * @param string  $label
     */
    public function add_value($name, $value, $is_parsed, $label)
    {
        $this->data[$name]   = $value;
        $this->labels[$name] = new Label(Label::GIVEN, $label, $name);

        if (isset($this->parsed_data[$name])) {
            unset($this->parsed_data[$name]);
        }

        if ($is_parsed) {
            $this->parsed_data[$name] = $value;
        }
    }

    /**
     * Parse a given value and return it
     *
     * @param string $name
     *
     * @return mixed
     */
    public function &get_value($name)
    {
        if (!isset($this->parsed_data[$name])) {
            if ($this->data[$name] instanceof Closure) {
                $this->parsed_data[$name] = $this->execute_callback($this->data[$name]);
            } else {
                $this->parsed_data[$name] = $this->data[$name];
            }
        }

        return $this->parsed_data[$name];
    }

    /**
     * Add an action to be run before the test (used in when)
     *
     * @param string|callable $name
     * @param callable        $callback
     * @param string          $label
     */
    public function add_action($name, $callback, $label)
    {
        if (!is_string($name)) {
            $this->actions[]                         = $name;
            $this->labels[count($this->actions) - 1] = new Label(Label::WHEN, $label);
        } else {
            $this->actions[$name] = $callback;
            $this->labels[$name]  = new Label(Label::WHEN, $label, $name);;
        }
    }

    /**
     * @param Exception|bool $e
     */
    public function addExpectedFailure($e = true) {
        $this->expectedFailure = $e;
    }

    /**
     * @param Exception $e
     *
     * @return bool
     */
    public function expectsFailure($e = null) {
        if ($e === null && $this->expectedFailure !== null) {
            return true;
        }

        return $this->expectedFailure === true || $e !== null && $e instanceof $this->expectedFailure;
    }

    /**
     * Return the description of the current test
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Return the corresponding label for the given name
     *
     * @param string $name
     *
     * @return string
     */
    public function labelFor($name)
    {
        return (isset($this->labels[$name]) ? $this->labels[$name] : $name);
    }

    /**
     * Return the labels array
     *
     * @return array
     */
    public function labels()
    {
        return $this->labels;
    }

    /**
     * @return callable
     */
    public function getLastCallback()
    {
        return $this->current_callback;
    }
}
