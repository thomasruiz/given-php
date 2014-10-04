<?php
namespace GivenPHP;

Interface IReporter {

    /**
     * Called at the start of a test run, should be used to print out any
     * intial reporter information
     * @param  string $version - GivenPHP version
     */
    public function reportStart($version);

    /**
     * Called to report a successful test, should be called for each test
     * that passes
     * @param  int    $count       - the number of tests executed so far
     * @param  string $description - description of the passing test
     */
    public function reportSuccess($count, $description);

    /**
     * Called to report a failing test, should be called for each test that
     * fails
     * @param  int    $count       - the number of tests executed so far
     * @param  string $description - description of the failing test
     */
    public function reportFailure($count, $description);

    /**
     * Called at the end of a test run, should be used to print out any
     * final reporter information
     * @param  int $total     - total number of tests run
     * @param  array $errors  - array of error objects
     * @param  array $labels  - array of labels corresponding to errors
     * @param  array $results - array of results
     */
    public function reportEnd($total, $errors, $labels, $results);
}