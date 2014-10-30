<?php
/**
 * Created by PhpStorm.
 * User: truiz
 * Date: 28/10/2014
 * Time: 13:41
 */

namespace GivenPHP\Reporting;

use GivenPHP\EnhancedCallback;
use GivenPHP\GivenPHP;
use GivenPHP\Runner;
use GivenPHP\TestCase;
use GivenPHP\TestResult;
use GivenPHP\TestSuite;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultReporter implements IReporter
{

    /**
     * The output used for writing
     *
     * @var OutputInterface $output
     */
    private $output;

    /**
     * The default indentation for long outputs
     *
     * @var string $indent
     */
    private $indent = '  ';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->output = new ConsoleOutput(OutputInterface::VERBOSITY_NORMAL, true);
    }

    /**
     * Notify that the runner started
     *
     * @param Runner $runner
     *
     * @return void
     */
    public function runnerStarted(Runner $runner)
    {
        $this->output->writeln("GivenPHP v" . GivenPHP::VERSION);
        $this->output->writeln('');
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
        $this->output->writeLn('');

        $givenPHP    = GivenPHP::getInstance();
        $testResults = $givenPHP->getTestResults();

        if ($givenPHP->hasError()) {
            $this->writeFailedTests($testResults);
        }

        $this->output->writeLn('');

        $this->output->writeLn($this->generateSummaryMessage($testResults, $givenPHP->hasError()));
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
        if ($testResult->isError()) {
            $this->output->write($this->colorize('F', 'red'));
        } else {
            $this->output->write('.');
        }
    }

    /**
     * Colorize a message
     *
     * @param string $message
     * @param string $color
     *
     * @return string
     */
    private function colorize($message, $color)
    {
        return "<fg=$color>$message</fg=$color>";
    }

    /**
     * @param TestResult[] $testResults
     *
     * @return string
     */
    private function generateSummaryMessage($testResults, $hasError)
    {
        if (!$hasError) {
            return $this->colorize(count($testResults) . ' examples, 0 failures', 'green');
        }

        $errors = 0;
        foreach ($testResults as $r) {
            if ($r->isError()) {
                $errors++;
            }
        }

        $summary = $this->colorize(count($testResults) . " examples, $errors failures", 'red');

        return $summary;
    }

    /**
     * Write the failed tests
     *
     * @param TestResult[] $results
     *
     * @return void
     */
    private function writeFailedTests($results)
    {
        $this->output->writeLn('');
        $i = 1;

        foreach ($results AS $r) {
            if ($r->isError()) {
                $this->writeFailedTest($r, $i++);
            }
        }
    }

    /**
     * Write a failed test
     *
     * @param TestResult $result
     *
     * @return void
     */
    private function writeFailedTest($result, $failedTestNumber)
    {
        $fct = new EnhancedCallback($result->getTestCase()->getCallback());

        $message = $this->indent . $failedTestNumber . ') ' . $result->getSuite()->getCurrentContext()->getLabel();
        $message .= $this->indent . 'Then { ' . $fct->code() . ' }';
        $this->output->writeln($message);
    }
}
