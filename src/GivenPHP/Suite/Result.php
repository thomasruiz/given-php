<?php namespace GivenPHP\Suite;

class Result
{

    /**
     * The actual result of the Test.
     *
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
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->result;
    }

    /**
     * Return false if the Test was a success.
     *
     * @return bool
     */
    public function isFailure()
    {
        return !$this->result;
    }
}
