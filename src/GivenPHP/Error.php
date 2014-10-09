<?php

namespace GivenPHP;

use Exception;

class Error
{
    /**
     * Constructor
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
    public static function assertHandler($file, $line, $code) {
        throw new AssertionException($file, $line, $code);
    }
} 
