<?php
namespace GivenPHP;

use GivenPHP\IReporter;
use GivenPHP\Output;

class DefaultReporter implements IReporter
{

    public function reportStart($version) {
        Output::message('GivenPHP v' . $version . PHP_EOL . PHP_EOL);
    }

    public function reportSuccess($count, $description) {
        Output::message('.');
    }

    public function reportFailure($count, $description) {
        Output::message('F', Output::RED);
    }

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