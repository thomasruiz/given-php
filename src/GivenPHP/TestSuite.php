<?php

namespace GivenPHP;

class TestSuite
{

    private $description;
    private $actions;
    private $data;
    private $parsed;

    /**
     * @var Callback $current_callback
     */
    private $current_callback;

    public function __construct($description)
    {
        $this->description = $description;
        $this->actions     = [ ];
        $this->data        = [ ];
        $this->parsed      = [ ];
    }

    public function run($callback)
    {
        // Ensure that the base context is clean for further tests
        $context = clone $this;

        $context->execute_actions();
        $result = $context->execute_callback($callback);
        return new TestResult($result, $context, $context->current_callback);
    }

    private function execute_actions()
    {
        foreach ($this->actions AS $action) {
            $this->execute_callback($action);
        }
    }

    private function execute_callback($action)
    {
        $callback = new EnhancedCallback($action);
        $this->current_callback = $callback;
        return $callback($this);
    }

    public function add_description($description)
    {
        $this->description .= ' ' . $description;
    }

    public function add_value($name, $value)
    {
        $this->data[$name] = $value;

        if (isset($this->parsed[$name])) {
            unset($this->parsed[$name]);
        }
    }

    public function &get_value($name)
    {
        if (!isset($this->parsed[$name])) {
            if (is_callable($this->data[$name])) {
                $this->parsed[$name] = $this->execute_callback($this->data[$name]);
            } else {
                $this->parsed[$name] = $this->data[$name];
            }
        }

        return $this->parsed[$name];
    }

    public function add_action($callback)
    {
        $this->actions[] = $callback;
    }

    public function description()
    {
        return $this->description;
    }
}
