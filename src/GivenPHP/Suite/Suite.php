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
     * The contexts of the suite.
     *
     * @var Context[]
     */
    private $contexts;

    /**
     * The context that the suite is running.
     *
     * @var Context
     */
    private $currentContext;

    /**
     * Construct a new Suite object.
     *
     * @param string   $label
     * @param callable $callback
     */
    public function __construct($label, $callback)
    {
        $this->currentContext = new Context($label, $callback);
        $this->contexts[]     = $this->currentContext;
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
        $result = true;

        foreach ($this->contexts as $context) {
            $this->currentContext = $context;
            $result &= $context->execute();
        }

        return $result;
    }

    /**
     * Isolate a new context.
     *
     * @param string   $label
     * @param callable $callback
     *
     * @return void
     */
    public function isolateContext($label, $callback)
    {
        $parent = $this->currentContext;

        $this->currentContext = new Context($label, $callback, $this->compiler, $parent);
        $this->currentContext->run();
        $this->contexts[] = $this->currentContext;

        $this->currentContext = $parent;
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
