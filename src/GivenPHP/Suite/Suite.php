<?php namespace GivenPHP\Suite;

use GivenPHP\Compiler\Compiler;

class Suite
{

    /**
     * The compiler used for raw values.
     *
     * @var Compiler $compiler
     */
    private $compiler;

    /**
     * Construct a new Suite object.
     *
     * @param string   $label
     * @param callable $callback
     */
    public function __construct($label, $callback)
    {
        $this->currentContext = new Context($label, $callback);
    }

    /**
     * Run the suite callback.
     *
     * @return bool
     */
    public function run()
    {
        return $this->currentContext->run();
    }

    /**
     * Execute the suite tests.
     *
     * @return bool
     */
    public function execute()
    {
        return $this->currentContext->execute();
    }

    /**
     * Get the current context that we are running.
     *
     * @return Context
     */
    public function getCurrentContext()
    {
        return $this->currentContext;
    }

    /**
     * Set the compiler for the suite.
     *
     * @param Compiler $compiler
     *
     * @return void
     */
    public function setCompiler(Compiler $compiler)
    {
        $this->compiler = $compiler;
        $this->currentContext->setCompiler($compiler);
    }
}
