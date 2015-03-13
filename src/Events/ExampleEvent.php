<?php namespace GivenPHP\Events;

use GivenPHP\TestSuite\Context;
use Symfony\Component\EventDispatcher\Event;

class ExampleEvent extends Event
{

    /**
     * @var bool
     */
    private $result;

    /**
     * @var callable
     */
    private $example;

    /**
     * @var Context
     */
    private $context;

    /**
     * Construct a new ExampleEvent object
     *
     * @param callable $example
     * @param Context  $context
     * @param bool     $result
     */
    public function __construct(callable $example, Context $context, $result)
    {
        $this->result  = $result;
        $this->example = $example;
        $this->context = $context;
    }

    /**
     * @return boolean
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return callable
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }
}