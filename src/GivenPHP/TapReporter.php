<?php
namespace GivenPHP;

use GivenPHP\IReporter;

class TapReporter implements IReporter
{

    public function reportStart($version) {
        //noop
    }

    public function reportSuccess($count, $description) {
        echo "ok {$count} {$description}" . PHP_EOL;
    }

    public function reportFailure($count, $description) {
        echo "not ok {$count} {$description}" . PHP_EOL;
    }

    public function reportEnd($total, $errors, $labels, $results) {
        if ($total > 0) echo "1..{$total}" . PHP_EOL;
    }
}