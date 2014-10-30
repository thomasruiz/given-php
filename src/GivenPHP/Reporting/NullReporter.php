<?php
/**
 * Created by PhpStorm.
 * User: truiz
 * Date: 30/10/2014
 * Time: 12:30
 */

namespace GivenPHP\Reporting;


use GivenPHP\Runner;
use GivenPHP\TestCase;
use GivenPHP\TestResult;
use GivenPHP\TestSuite;

class NullReporter implements IReporter
{

    /**
     * Notify that the runner started
     *
     * @param Runner $runner
     *
     * @return void
     */
    public function runnerStarted(Runner $runner)
    {
        // do nothing
    }

    /**
     * Notify that the runner ended
     *
     * @param Runner $runner
     *
     * @return void
     */
    public function runnerEnded(Runner $runner)
    {
        // do nothing
    }

    /**
     * Notify that a new suite (file in most cases) started execution
     *
     * @param TestSuite $suite
     *
     * @return void
     */
    public function suiteStarted(TestSuite $suite)
    {
        // do nothing
    }

    /**
     * Notify that a suite is finished
     *
     * @param TestSuite $suite
     *
     * @return void
     */
    public function suiteEnded(TestSuite $suite)
    {
        // do nothing
    }

    /**
     * Notify that a test started
     *
     * @param TestCase $testCase
     *
     * @return void
     */
    public function testStarted(TestCase $testCase)
    {
        // do nothing
    }

    /**
     * Notify that a test is finished
     *
     * @param TestResult $testResult
     *
     * @return void
     */
    public function testEnded(TestResult $testResult)
    {
        // do nothing
    }
}
