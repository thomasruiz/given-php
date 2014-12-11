<?php namespace GivenPHP\Suite;

use GivenPHP\Compiler\Compiler;

class Suite
{

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
     * @return Context
     */
    public function getCurrentContext()
    {
        return $this->currentContext;
    }

    /**
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
