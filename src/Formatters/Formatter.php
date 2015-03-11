<?php namespace GivenPHP\Formatters;

use GivenPHP\Events\ExampleEvent;
use GivenPHP\Events\SuiteEvent;

abstract class Formatter
{
    /**
     * @param SuiteEvent $suiteEvent
     */
    public function beforeSuite(SuiteEvent $suiteEvent)
    {
    }

    /**
     * @param SuiteEvent $suiteEvent
     */
    public function afterSuite(SuiteEvent $suiteEvent)
    {
    }

    /**
     * @param ExampleEvent $exampleEvent
     */
    public function beforeExample(ExampleEvent $exampleEvent)
    {
    }

    /**
     * @param ExampleEvent $exampleEvent
     */
    public function afterExample(ExampleEvent $exampleEvent)
    {
    }
}