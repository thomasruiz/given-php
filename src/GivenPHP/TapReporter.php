<?php
namespace GivenPHP;

use GivenPHP\IReporter;
use GivenPHP\Output;

class TapReporter implements IReporter
{

    /**
     * Performs no actions, implemented to meet IReporter spec
     */
    public function reportStart($version) {
        //noop
    }

    /**
     * Prints a tap compatible success output
     * @example ok 3 math module basic addition of 3 + 3 
     */
    public function reportSuccess($count, $description) {
        Output::message("ok {$count} {$description}" . PHP_EOL);
    }

    /**
     * Prints a tap compatible failure output
     * @example not ok 3 math module basic addition of 3 + 3 
     */
    public function reportFailure($count, $description) {
        Output::message("not ok {$count} {$description}" . PHP_EOL);
    }

    /**
     * Prints out a tap compatible summary line. The tap format specifies
     * that this line can be printed before the tests or after. In this case
     * we have chosen after since we then know how many tests were run
     * @example 1..14
     */
    public function reportEnd($total, $errors, $labels, $results) {
        if ($total > 0) Output::message("1..{$total}" . PHP_EOL);
    }
}