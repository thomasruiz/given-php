<?php

namespace GivenPHP;

class TestCase
{

    /**
     * The callback corresponding to the then() statement
     *
     * @var callable $callback
     */
    private $callback;

    /**
     * Constructor
     *
     * @param callable $callback
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
        $suite->executeActions();
        $result = $suite->executeCallback($this->callback);

        return new TestResult($result, $suite, $this);
    }
}
