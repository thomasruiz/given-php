<?php

namespace GivenPHP;

class TestResult
{

    /**
     * @var boolean $result
     */
    private $result;

    /**
     * @var TestSuite $context
     */
    private $context;

    /**
     * @var EnhancedCallback $callback
     */
    private $callback;

    /**
     * @var array $stack
     */
    private $stack;

    /**
     * @param                             $result
     * @param                             $context
     * @param \GivenPHP\EnhancedCallback  $callback
     */
    public function __construct($result, $context, EnhancedCallback $callback)
    {
        $this->result   = $result;
        $this->context  = $context;
        $this->callback = $callback;

        try {
            throw new \Exception;
        } catch (\Exception $e) {
            $this->stack = $e->getTrace();
        }
    }

    public function is_error()
    {
        return $this->result === false;
    }

    public function summary()
    {
        echo PHP_EOL . PHP_EOL;

        $red   = chr(27) . '[31m';
        $blue  = chr(27) . '[34m';

        echo $red . $this->callback->file() . ':' . $this->callback->line() . $blue . ' # ' . $this->context->description() .
             '  Then { ' . $this->callback->code() . ' } ';
    }

    public function render($n)
    {
        echo PHP_EOL . PHP_EOL;

        $red   = chr(27) . '[31m';
        $blue  = chr(27) . '[34m';
        $white = chr(27) . '[0m';

        echo <<<FAILURE
  $n) {$this->context->description()}   Then { {$this->callback->code()} }

     {$red}Failure/Error: Then { {$this->callback->code()} }
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

    private function format_value($value) {
        return is_object($value) || is_array($value) ? gettype($value) : $value;
    }
}
