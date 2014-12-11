<?php namespace GivenPHP\Runner;

use GivenPHP\Compiler\Compiler;
use GivenPHP\Suite\Suite;

class DefaultRunner
{

    /**
     * Paths to the file(s) that the Runner will require.
     *
     * @var string[]
     */
    protected $paths;

    /**
     * Construct a new DefaultRunner object.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->paths = $path;
    }

    /**
     * Run all test suites in the paths informed in Constructor.
     *
     * @return bool
     */
    public function run()
    {
        $result = true;

        foreach ($this->paths as $path) {
            $result &= $this->runSuite($path);
        }

        return $result;
    }

    /**
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
