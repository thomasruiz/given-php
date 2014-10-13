<?php
namespace GivenPHP\Reporting;

use GivenPHP\Label;
use GivenPHP\Output;
use GivenPHP\TestResult;

/**
 * The default reporter for GivenPHP
 * If no reporter is specified, this reporter will be used
 */
class ListReporter implements IReporter
{

    /**
     * Outputs GivenPHP version and an empty line
     *
     * @param string $version
     */
    public function reportStart($version)
    {
        Output::message('GivenPHP v' . $version . PHP_EOL . PHP_EOL);
    }

    /**
     * Prints out a simple . character for each passing test
     *
     * @param int    $count
     * @param string $description
     */
    public function reportSuccess($count, $description)
    {
        Output::message(json_decode('"\u2713"'), Output::GREEN);
        Output::message(' ');
        Output::message($description);
        Output::message(PHP_EOL);
    }

    /**
     * Prints an F character for each failing test
     *
     * @param int    $count
     * @param string $description
     */
    public function reportFailure($count, $description)
    {
        Output::message(json_decode('"\u2717"'), Output::RED);
        Output::message(' ');
        Output::message($description);
        Output::message(PHP_EOL);
    }

    /**
     * Renders any errors with matching labels
     *
     * @param TestResult[] $errors
     * @param Label[]      $labels
     */
    private function renderErrors($errors, $labels)
    {
        foreach ($errors AS $i => $error) {
            $error->render($i + 1, $labels[$i]);
        }
    }

    /**
     * Renders a status message using total and totalErrors values
     *
     * @param int $total
     * @param int $totalErrors
     */
    private function renderStatusMessage($total, $totalErrors)
    {
        $hasErrors = $totalErrors > 0;
        $message   = PHP_EOL . PHP_EOL . $total . ' examples, ' . $totalErrors . ' failures';
        Output::message($message, $hasErrors ? Output::RED : Output::GREEN);
    }

    /**
     * Renders a 'Failed examples' error summary block
     *
     * @param TestResult[] $errors
     */
    private function renderErrorSummaries($errors)
    {
        if (!empty($errors)) {
            Output::message(PHP_EOL . PHP_EOL . 'Failed examples:');
        }

        foreach ($errors AS $error) {
            $error->summary();
        }
    }

    /**
     * Prints out a result summary and if there are any test failures,
     * prints out error details
     *
     * @param int          $total   - total number of tests run
     * @param TestResult[] $errors  - array of error objects
     * @param Label[]      $labels  - array of labels corresponding to errors
     * @param TestResult[] $results - array of results
     */
    public function reportEnd($total, $errors, $labels, $results)
    {
        $this->renderErrors($errors, $labels);
        $this->renderStatusMessage($total, count($errors));
        $this->renderErrorSummaries($errors);
        Output::message(PHP_EOL);
    }
}
