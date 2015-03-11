<?php namespace GivenPHP\Events;

use Symfony\Component\EventDispatcher\Event;

class ExampleEvent extends Event
{

    /**
     * @var bool
     */
    private $result;

    /**
     * Construct a new ExampleEvent object
     *
     * @param bool $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return boolean
     */
    public function getResult()
    {
        return $this->result;
    }
}