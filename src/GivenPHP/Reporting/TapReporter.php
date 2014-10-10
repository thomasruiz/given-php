<?php
namespace GivenPHP\Reporting;

use GivenPHP\Label;
use GivenPHP\Output;
use GivenPHP\TestResult;

class TapReporter implements IReporter
{

    /**
     * Performs no actions, implemented to meet IReporter spec
     *
     * @param string $version
     */
    public function reportStart($version)
    {
        //noop
    }

    /**
     * Prints a tap compatible success output
     *
     * @param int    $count
     * @param string $description
     */
    public function reportSuccess($count, $description)
    {
        Output::message("ok {$count} {$description}" . PHP_EOL);
    }

    /**
     * Prints a tap compatible failure output
     *
     * @param int    $count
     * @param string $description
     */
    public function reportFailure($count, $description)
    {
        Output::message("not ok {$count} {$description}" . PHP_EOL);
    }

    /**
     * Prints out a tap compatible summary line. The tap format specifies
     * that this line can be printed before the tests or after. In this case
     * we have chosen after since we then know how many tests were run
     *
     * @param int   $total
     * @param TestResult[] $errors
     * @param Label[] $labels
     * @param TestResult[] $results
     */
    public function reportEnd($total, $errors, $labels, $results)
    {
        if ($total > 0) {
            Output::message("1..{$total}" . PHP_EOL);
        }
    }
}
