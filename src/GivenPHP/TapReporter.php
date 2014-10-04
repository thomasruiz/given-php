<?php
namespace GivenPHP;

use GivenPHP\IReporter;
use GivenPHP\Output;

class TapReporter implements IReporter
{

    public function reportStart($version) {
        //noop
    }

    public function reportSuccess($count, $description) {
        Output::message("ok {$count} {$description}" . PHP_EOL);
    }

    public function reportFailure($count, $description) {
        Output::message("not ok {$count} {$description}" . PHP_EOL);
    }

    public function reportEnd($total, $errors, $labels, $results) {
        if ($total > 0) Output::message("1..{$total}" . PHP_EOL);
    }
}