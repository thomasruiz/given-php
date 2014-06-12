<?php

namespace GivenPHP;

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
    private $data = [ ];

    /**
     * The list of the functions to execute added by the 'when' function
     *
     * @var callable[]
     */
    private $actions = [ ];

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
    private $parsed_data = [ ];

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
        foreach ($this->actions AS $action) {
            $this->execute_callback($action);
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
     * @return void
     */
    public function add_description($description)
    {
        $this->description .= ' ' . $description;
    }

    /**
     * Add a value to be parsed (used in given)
     *
     * @param string $name
     * @param mixed $value
     */
    public function add_value($name, $value)
    {
        $this->data[$name] = $value;

        if (isset($this->parsed_data[$name])) {
            unset($this->parsed_data[$name]);
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
            if (is_callable($this->data[$name])) {
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
     * @param callable $callback
     */
    public function add_action($callback)
    {
        $this->actions[] = $callback;
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
}
