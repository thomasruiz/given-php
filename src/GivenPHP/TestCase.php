<?php

namespace GivenPHP;

use GivenPHP\Expectation\Expectation;

class TestCase
{

    /**
     * The callback corresponding to the then() statement
     *
     * @var callable $callback
     */
    private $callback;

    /**
     * The last callback ran
     *
     * @var callable $lastExecutedCallback
     */
    private $lastExecutedCallback;

    /**
     * The label of the complete context used for this test
     *
     * @var string $label
     */
    private $label;

    /**
     * Constructor
     *
     * @param callable|Expectation $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Run the test case with all its dependencies
     *
     * @param TestSuite $suite
     *
     * @return TestResult
     */
    public function run(TestSuite $suite)
    {
        $suite->reset();

        $this->label = $suite->getCurrentContext()->getLabel();

        try {
            $result = $this->runTest($suite);
        } catch (\Exception $e) {
            $result = $e;
        }

        $this->lastExecutedCallback = $suite->getCurrentContext()->getCurrentCallback();
        $result = $this->checkExpectation($result);

        return new TestResult($result, $suite, $this);
    }

    /**
     * Getter for $callback
     *
     * @return callable|Expectation
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * True if $callback is an instance of Expectation
     *
     * @return bool
     */
    public function isExpectation()
    {
        return $this->callback instanceof Expectation;
    }

    /**
     * Getter for $lastExecutedCallback
     *
     * @return callable
     */
    public function getLastExecutedCallback()
    {
        return $this->lastExecutedCallback;
    }

    /**
     * Getter for $label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Run the actual test within the suite
     *
     * @param $suite
     *
     * @return bool|null
     */
    private function runTest($suite)
    {
        $suite->setUp();
        $suite->executeActions();

        $result = !$this->callback instanceof Expectation ? $suite->executeCallback($this->callback) : null;

        $suite->tearDown();

        return $result;
    }

    /**
     * Run the expectation checks if needed
     *
     * @param bool|\Exception $result
     *
     * @return bool
     */
    private function checkExpectation($result)
    {
        if ($this->callback instanceof Expectation) {
            return $this->callback->check($result);
        }

        return $result;
    }
}
