<?php

namespace GivenPHP\Reporting;

use GivenPHP\Runner;
use GivenPHP\TestCase;
use GivenPHP\TestResult;
use GivenPHP\TestSuite;

interface IReporter
{
    /**
     * Notify that the runner started
     *
     * @param Runner $runner
     *
     * @return void
     */
    public function runnerStarted(Runner $runner);

    /**
     * Notify that the runner ended
     *
     * @param Runner $runner
     *
     * @return void
     */
    public function runnerEnded(Runner $runner);

    /**
     * Notify that a new suite (file in most cases) started execution
     *
     * @param TestSuite $suite
     *
     * @return void
     */
    public function suiteStarted(TestSuite $suite);

    /**
     * Notify that a suite is finished
     *
     * @param TestSuite $suite
     *
     * @return void
     */
    public function suiteEnded(TestSuite $suite);

    /**
     * Notify that a test started
     *
     * @param TestCase $testCase
     *
     * @return void
     */
    public function testStarted(TestCase $testCase);

    /**
     * Notify that a test is finished
     *
     * @param TestResult $testResult
     *
     * @return void
     */
    public function testEnded(TestResult $testResult);
}
