<?php namespace GivenPHP\Suite;

class Result
{

    /**
     * @var bool
     */
    private $result;

    /**
     * Construct a new Result object.
     *
     * @param bool $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Return true if the Test was a success.
     */
    public function isSuccess()
    {
        return $this->result;
    }
}
