<?php namespace GivenPHP\Suite;

use GivenPHP\Compiler\Compiler;
use GivenPHP\Value\EmptyValue;
use GivenPHP\Value\RawValue;

class Context
{

    /**
     * The label explaining the context.
     *
     * @var string
     */
    private $label;

    /**
     * The callback to be run, defining the context.
     *
     * @var callable
     */
    private $callback;

    /**
     * All values to compile necessary to the test, within this context.
     *
     * @var RawValue[]
     */
    private $rawValues = [ ];

    /**
     * All actions to compile before running the test, within this context.
     *
     * @var RawValue[]
     */
    private $actions = [ ];

    /**
     * The compiled values issued from actions and raw values.
     *
     * @var array
     */
    private $compiledValues = [ ];

    /**
     * All tests to run, within this context.
     *
     * @var Test[]
     */
    private $tests = [ ];

    /**
     * All test results, within this context.
     *
     * @var Result[]
     */
    private $results = [ ];

    /**
     * The compiler used for raw values.
     *
     * @var Compiler
     */
    private $compiler;

    /**
     * Construct a new Context object.
     *
     * @param string   $label
     * @param string   $callback
     * @param Compiler $compiler
     * @param Context  $parent
     */
    public function __construct($label, $callback, Compiler $compiler = null, Context $parent = null)
    {
        $this->label    = $label;
        $this->callback = $callback;
        $this->compiler = $compiler;

        if ($parent !== null) {
            $this->compiledValues = $parent->compiledValues;
            $this->rawValues      = $parent->rawValues;
            $this->actions        = $parent->actions;
            $this->label          = $parent->getLabel() . ' ' . $this->label;
        }
    }

    /**
     * Run the Context callback.
     *
     * @return bool
     */
    public function run()
    {
        $cb = $this->callback;

        return $cb() === null;
    }

    /**
     * Execute the tests.
     *
     * @return bool
     */
    public function execute()
    {
        $success = true;

        foreach ($this->tests as $test) {
            $success &= $this->executeTest($test);
        }

        return $success;
    }

    /**
     * Execute the actions registered for this context.
     *
     * @return void
     */
    public function executeActions()
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === EmptyValue::class) {
                $this->compiledValues[] = $this->compiler->compile($action, $this);
            } else {
                $this->compiledValues[ $action->getName() ] = $this->compiler->compile($action, $this);
            }
        }
    }

    /**
     * Compile the value if needed, and return a reference to it.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function &getValue($name)
    {
        if (!isset( $this->compiledValues[ $name ] )) {
            $this->compiledValues[ $name ] = $this->compiler->compile($this->rawValues[ $name ], $this);
        }

        return $this->compiledValues[ $name ];
    }

    /**
     * Add a given value to the context.
     *
     * @param string $label
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function addValue($label, $name, $value)
    {
        $this->rawValues[ $name ] = new RawValue($label, $name, $value);
    }

    /**
     * Add an action to execute before the tests.
     *
     * @param string   $label
     * @param string   $name
     * @param callable $action
     *
     * @return void
     */
    public function addAction($label, $name, $action)
    {
        $this->actions[] = new RawValue($label, $name, $action);
    }

    /**
     * Add a test to the suite.
     *
     * @param string   $label
     * @param callable $test
     *
     * @return void
     */
    public function addTest($label, $test)
    {
        $this->tests[] = new Test($label, $test);
    }

    /**
     * Get the compiler for the context.
     *
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * Set the compiler for the context.
     *
     * @param Compiler $compiler
     *
     * @return void
     */
    public function setCompiler(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Get the label for the context.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Execute a single test.
     *
     * @param Test $test
     *
     * @return bool
     */
    protected function executeTest(Test $test)
    {
        $this->cleanUp();

        $result = $test->execute($this);

        $this->results[] = $result;

        if ($result->isSuccess()) {
            echo '.';
        } else {
            echo 'F';
        }

        return $result->isSuccess();
    }

    /**
     * Clean up the context for another test.
     *
     * @return void
     */
    protected function cleanUp()
    {
        $this->compiledValues = [ ];
    }
}
