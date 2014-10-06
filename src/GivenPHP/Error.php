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
} 
