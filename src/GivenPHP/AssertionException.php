<?php

namespace GivenPHP;

use Exception;

class AssertionException extends Exception
{
    /**
     * Constructor
     *
     * @param string $file
     * @param int    $line
     * @param string $code
     */
    public function __construct($file, $line, $code)
    {
        parent::__construct("Assertion failed at $file($line)");
    }
} 
