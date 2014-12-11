<?php namespace GivenPHP\Runner;

use GivenPHP\Compiler\Compiler;
use GivenPHP\Runner\Contracts\Runner;
use GivenPHP\Suite\Suite;

class DefaultRunner implements Runner
{

    /**
     * Construct a new DefaultRunner object.
     */
    public function __construct()
    {
    }

    /**
     * Run all test suites in the given paths.
     *
     * @param string[] $paths
     *
     * @return bool
     */
    public function run($paths)
    {
        $result = true;

        foreach ($paths as $path) {
            $result &= $this->runSuite($path);
        }

        return $result;
    }

    /**
     * Run a single suite in a file.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function runSuite($path)
    {
        /** @var Suite $suite */
        $suite = require $path;

        $suite->setCompiler($compiler = new Compiler);

        $suite->run();

        return $suite->execute();
    }
}
