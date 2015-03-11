<?php namespace GivenPHP\TestSuite;

use Countable;

class Suite implements Countable
{

    /**
     * @var Specification[]
     */
    private $specs = [ ];

    /**
     * @var float
     */
    private $startTime;

    /**
     * @var float
     */
    private $endTime;

    /**
     * Add a spec to the test suite.
     *
     * @param Specification $spec
     */
    public function addSpecification(Specification $spec)
    {
        $this->specs[] = $spec;
        $spec->setSuite($this);
    }

    /**
     * Get all the specs of the suite.
     *
     * @return Specification[]
     */
    public function getSpecifications()
    {
        return $this->specs;
    }

    /**
     * Count all the examples in the suite.
     *
     * @return number
     */
    public function count()
    {
        return array_sum(array_map('count', $this->specs));
    }

    /**
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param float $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return float
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param float $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }
}