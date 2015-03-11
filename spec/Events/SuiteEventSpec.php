<?php namespace spec\GivenPHP\Events;

use GivenPHP\Events\SuiteEvent;
use GivenPHP\TestSuite\Suite;

return describe(SuiteEvent::class, with('suite'), function () {
    given('suite', function (Suite $suiteProphecy) { return $suiteProphecy->reveal(); });

    context('.getTime', function () {
        given(function (Suite $suiteProphecy) { $suiteProphecy->getStartTime()->willReturn(5.); });
        given(function (Suite $suiteProphecy) { $suiteProphecy->getEndTime()->willReturn(8.); });
        then(function (SuiteEvent $that) { return $that->getTime() === 3.; });
    });

    context('.getLoadingTime', function () {
        given(function (Suite $suiteProphecy) { $suiteProphecy->getLoadingStartTime()->willReturn(4.); });
        given(function (Suite $suiteProphecy) { $suiteProphecy->getLoadingEndTime()->willReturn(9.); });
        then(function (SuiteEvent $that) { return $that->getLoadingTime() === 5.; });
    });

    context('.getTotalExamples', function () {
        given(function (Suite $suiteProphecy) { $suiteProphecy->count()->willReturn(10); });
        then(function (SuiteEvent $that) { return $that->getTotalExamples() === 10; });
    });

    context('.getTotalSpecs', function () {
        given(function (Suite $suiteProphecy) { $suiteProphecy->getSpecifications()->willReturn([ 'foo' ]); });
        then(function (SuiteEvent $that) { return $that->getTotalSpecifications() === 1; });
    });
});