<?php
namespace GivenPHP;

Interface IReporter {
    public function reportStart($version);
    public function reportSuccess($count, $description);
    public function reportFailure($count, $description);
    public function reportEnd($total, $errors, $labels, $results);
}