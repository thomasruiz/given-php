<?php

namespace GivenPHP;

use Exception;

class PHPErrorException extends Exception
{
    /**
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param array  $errcontext
     */
    public function __construct($errno, $errstr, $errfile, $errline, $errcontext)
    {
        parent::__construct("Error $errno at $errfile($errline): $errstr");
    }
}
