<?php namespace spec\GivenPHP\Events;

use GivenPHP\Events\ExampleEvent;
use GivenPHP\TestSuite\Context;

return describe(ExampleEvent::class, with('example', 'context', 'result'), function () {
    given('result', function () { return true; });
    given('example', function () { return function () {}; });
    given('context', function (Context $contextProphecy) { return $contextProphecy->reveal(); });

    then(function (ExampleEvent $that) { return $that->getResult() === true; });
    then(function (ExampleEvent $that, $context) { return $that->getContext() === $context; });
    then(function (ExampleEvent $that, $example) { return $that->getExample() === $example; });
});