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
     * @var float
     */
    private $loadingStartTime;

    /**
     * @var float
     */
    private $loadingEndTime;

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

    /**
     * @return float
     */
    public function getLoadingStartTime()
    {
        return $this->loadingStartTime;
    }

    /**
     * @param float $loadingStartTime
     */
    public function setLoadingStartTime($loadingStartTime)
    {
        $this->loadingStartTime = $loadingStartTime;
    }

    /**
     * @return float
     */
    public function getLoadingEndTime()
    {
        return $this->loadingEndTime;
    }

    /**
     * @param float $loadingEndTime
     */
    public function setLoadingEndTime($loadingEndTime)
    {
        $this->loadingEndTime = $loadingEndTime;
    }
}