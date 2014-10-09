<?php

namespace GivenPHP;

use Exception;

class Error
{
    /**
     * Constructor
     *
     * @param Exception $e
     */
    public function __construct(Exception $e = null)
    {
        $this->exception = $e;
    }

    /**
     * @param string $file
     * @param string $line
     * @param string $code
     *
     * @throws AssertionException
     */
    public static function assertHandler($file, $line, $code)
    {
        throw new AssertionException($file, $line, $code);
    }

    /**
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param array  $errcontext
     *
     * @throws PHPErrorException
     */
    public static function errorHandler($errno, $errstr, $errfile = null, $errline = null, $errcontext = [])
    {
        if (($errno & (E_NOTICE | E_STRICT)) != 0) {
            return ;
        }

        throw new PHPErrorException($errno, $errstr, $errfile, $errline, $errcontext);
    }
} 
