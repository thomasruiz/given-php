<?php namespace GivenPHP\Runner\Contracts;

interface Runner
{
    /**
     * Run all test suites in the given paths.
     *
     * @param string[] $paths
     *
     * @return bool
     */
    public function run($paths);
}
