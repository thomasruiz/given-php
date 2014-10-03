<?php
namespace GivenPHP;

use GivenPHP\IReporter;

class DefaultReporter implements IReporter
{

    public function reportStart($version) {
        echo 'GivenPHP v' . $version . PHP_EOL . PHP_EOL;
    }

    public function reportSuccess($count, $description) {
        echo '.';
    }

    public function reportFailure($count, $description) {
        echo chr(27) . '[31mF' . chr(27) . '[0m';
    }

    public function reportEnd($total, $errors, $labels, $results) {
        if (!empty($errors)) {
            foreach ($errors AS $i => $error) {
                $error->render($i + 1, $labels[$i]);
            }

            echo PHP_EOL . PHP_EOL . chr(27) . '[31m' . count($results) . ' examples, ' . count($errors) .
                 ' failures';

            echo PHP_EOL . PHP_EOL . chr(27) . '[0m' . 'Failed examples:';

            foreach ($errors AS $error) {
                $error->summary();
            }

            echo PHP_EOL;
        } else {
            echo PHP_EOL . PHP_EOL . chr(27) . '[32m' . count($results) . ' examples, 0 failures';
        }

        echo chr(27) . '[0m' . PHP_EOL;
    }
}