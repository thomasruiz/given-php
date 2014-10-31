<?php

namespace GivenPHP\Expectation;

class Failure extends Expectation
{

    /**
     * The expected error, can be null
     *
     * @var string $expectedError
     */
    private $expectedError;

    /**
     * Constructor
     *
     * @param string $expectedError
     */
    public function __construct($expectedError = null)
    {
        $this->expectedError = $expectedError;
    }

    /**
     * @param bool|\Exception $testResult
     *
     * @return bool
     */
    public function check($testResult)
    {
        if ($testResult instanceof \Exception) {
            if ($this->expectedError === null || $testResult instanceof $this->expectedError) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the message used by reporters in case of failures
     *
     * @return string
     */
    public function expectsMessage()
    {
        $message = 'failure';
        if ($this->expectedError !== null) {
            $message .= ' with ' . $this->expectedError;
        }

        return $message;
    }
}
