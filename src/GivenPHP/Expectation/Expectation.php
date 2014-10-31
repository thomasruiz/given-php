<?php

namespace GivenPHP\Expectation;

abstract class Expectation
{
    /**
     * Check if the expectation is met
     *
     * @param bool|\Exception $testResult
     *
     * @return bool
     */
    public abstract function check($testResult);

    /**
     * Generate the message used by reporters in case of failures
     *
     * @return string
     */
    public abstract function expectsMessage();
}
