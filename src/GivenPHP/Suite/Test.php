<?php namespace GivenPHP\Suite;

class Test
{

    /**
     * The result of the test.
     *
     * @var Result
     */
    private $result;

    /**
     * @var string
     */
    private $label;

    /**
     * The callback the test represent.
     * It must return a boolean that tells if the test succeeded.
     *
     * @var callable
     */
    private $callback;

    /**
     * Construct a new Test object.
     *
     * @param $label
     * @param $callback
     */
    public function __construct($label, $callback)
    {
        $this->label    = $label;
        $this->callback = $callback;
    }

    /**
     * Execute the test.
     *
     * @param Context $context
     *
     * @return Result
     */
    public function execute(Context $context)
    {
        $context->executeActions();
        $result       = $context->getCompiler()->executeCallback($this->callback, $context);
        $this->result = new Result($result);

        return $this->result;
    }
}
