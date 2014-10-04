<?php
namespace GivenPHP;

use GivenPHP\IReporter;
use GivenPHP\Output;

/**
 * The default reporter for GivenPHP
 * If no reporter is specified, this reporter will be used
 */
class DefaultReporter implements IReporter
{

    /**
     * Outputs GivenPHP version and an empty line
     */
    public function reportStart($version) {
        Output::message('GivenPHP v' . $version . PHP_EOL . PHP_EOL);
    }

    /**
     * Prints out a simple . character for each passing test
     */
    public function reportSuccess($count, $description) {
        Output::message('.');
    }

    /**
     * Prints an F character for each failing test
     */
    public function reportFailure($count, $description) {
        Output::message('F', Output::RED);
    }

    /**
     * Prints out a result summary and if there are any test failures,
     * prints out error details
     */
    public function reportEnd($total, $errors, $labels, $results) {
        if (!empty($errors)) {
            foreach ($errors AS $i => $error) {
                $error->render($i + 1, $labels[$i]);
            }

            $message  = PHP_EOL . PHP_EOL;
            $message .= $total . ' examples, ' . count($errors) . ' failures';
            Output::message($message, Output::RED);
            
            Output::message(PHP_EOL . PHP_EOL . 'Failed examples:');

            foreach ($errors AS $error) {
                $error->summary();
            }

            Output::message(PHP_EOL);
        } else {
            $message = PHP_EOL . PHP_EOL . $total . ' examples, 0 failures';
            Output::message($message, Output::GREEN);
        }

        Output::message(PHP_EOL);
    }
}