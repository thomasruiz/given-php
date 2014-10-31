<?php

namespace GivenPHP;

class TestResult
{

    /**
     * The result of the test case, whether it succeeded or not
     *
     * @var boolean|object $result
     */
    private $result;

    /**
     * The test suite used for the test case
     *
     * @var TestSuite $suite
     */
    private $suite;

    /**
     * The corresponding test case
     *
     * @var TestCase $testCase
     */
    private $testCase;

    /**
     * Constructor
     *
     * @param $result
     * @param $suite
     * @param $testCase
     */
    public function __construct($result, $suite, $testCase)
    {
        $this->result   = $result;
        $this->suite    = $suite;
        $this->testCase = $testCase;
    }

    /**
     * Whether the result is an error or not
     *
     * @return bool
     */
    public function isError()
    {
        return $this->result !== true;
    }

    /**
     * Getter of $suite
     *
     * @return TestSuite
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * Getter of $testCase
     *
     * @return TestCase
     */
    public function getTestCase()
    {
        return $this->testCase;
    }

    /**
     * Getter for $result
     *
     * @return boolean|object
     */
    public function getResult()
    {
        return $this->result;
    }
}
