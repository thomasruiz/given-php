<?php namespace GivenPHP\Events;

use GivenPHP\TestSuite\Suite;
use Symfony\Component\EventDispatcher\Event;

class SuiteEvent extends Event
{

    /**
     * @var Suite
     */
    private $suite;

    /**
     * Construct a new SuiteEvent object
     *
     * @param Suite $suite
     */
    public function __construct(Suite $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->suite->getEndTime() - $this->suite->getStartTime();
    }

    /**
     * @return float
     */
    public function getLoadingTime()
    {
        return $this->suite->getLoadingEndTime() - $this->suite->getLoadingStartTime();
    }

    /**
     * @return int
     */
    public function getTotalSpecifications()
    {
        return count($this->suite->getSpecifications());
    }

    /**
     * @return int
     */
    public function getTotalExamples()
    {
        return count($this->suite);
    }
}