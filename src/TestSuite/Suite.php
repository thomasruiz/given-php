<?php namespace GivenPHP\TestSuite;

use Countable;

class Suite implements Countable
{

    /**
     * @var Specification[]
     */
    private $specs = [];

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
}