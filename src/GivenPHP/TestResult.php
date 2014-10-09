<?php

namespace GivenPHP;

use Exception;

/**
 * Class TestResult
 *
 * @package GivenPHP
 */
class TestResult
{

    /**
     * Whether the test passed or failed
     *
     * @var boolean $result
     */
    private $result;

    /**
     * The context of the test
     *
     * @var TestSuite $context
     */
    private $context;

    /**
     * The test that actually failed/passed
     *
     * @var EnhancedCallback $callback
     */
    private $callback;

    /**
     * The stack of errors in case of a failed test
     *
     * @var string[] $stack
     */
    private $stack;

    /**
     * Constructor
     *
     * @param boolean          $result
     * @param TestSuite        $context
     * @param EnhancedCallback $callback
     */
    public function __construct($result, $context, EnhancedCallback $callback)
    {
        $this->result   = $result;
        $this->context  = $context;
        $this->callback = $callback;

        try {
            throw new Exception;
        } catch (Exception $e) {
            $this->stack = $e->getTrace();
        }
    }

    /**
     * Return true if the test failed, false otherwise
     *
     * @return bool
     */
    public function is_error()
    {
        return $this->result === false;
    }

    /**
     * Output a summary for a failing test
     *
     * @return void
     */
    public function summary()
    {
        echo PHP_EOL . PHP_EOL;

        $red  = chr(27) . '[31m';
        $blue = chr(27) . '[34m';

        echo $red . $this->callback->file() . ':' . $this->callback->line() . $blue . ' # ' .
             $this->context->description() . '  Then { ' . $this->callback->code() . ' } ';
    }

    /**
     * Render a failing test
     *
     * @param int    $n
     * @param string $label
     *
     * @return void
     */
    public function render($n, $label)
    {
        echo PHP_EOL . PHP_EOL;

        $red   = chr(27) . '[31m';
        $blue  = chr(27) . '[34m';
        $white = chr(27) . '[0m';

        $completeLabel = '';
        if ($label !== null) {
            $labels = $this->context->labels();
            foreach ($labels as $i => $l) {
                if (!$l->isEmpty()) {
                    $completeLabel .= '       ' . $l . ' -> ' . $this->format_value($this->context->get_value($i)) . PHP_EOL;
                }
            }
            $completeLabel .= '       Then ' . $label . PHP_EOL;
        }

        echo <<<FAILURE
  $n) {$this->context->description()}   Then { {$this->callback->code()} }

     {$red}Failure/Error: Then { {$this->callback->code()} }
$completeLabel
       Then expression failed at {$this->callback->file()}:{$this->callback->line()}
FAILURE;

        $parameters = $this->callback->parameters($this->context);
        foreach ($parameters AS $i => $parameter) {
            if (is_int($i)) {
                continue;
            }

            echo PHP_EOL . "       {$this->format_value($parameter)} <- {$i}";
        }

        echo $blue;

        foreach ($this->stack AS $error) {
            echo PHP_EOL . "     # {$error['file']}:{$error['line']}:in `{$error['function']}'";
        }

        echo $white;
    }

    /**
     * Return the type of the value or the value itself if printable
     *
     * @param mixed $value
     *
     * @return string
     */
    private function format_value($value)
    {
        return is_array($value) ? 'Array(' . count($value) . ')' : (is_object($value) ? get_class($value) : $value);
    }
}
