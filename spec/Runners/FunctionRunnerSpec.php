<?php namespace spec\GivenPHP\Runners;

use GivenPHP\Runners\FunctionRunner;
use GivenPHP\TestSuite\Context;
use Prophecy\Argument;
use Prophecy\Prophet;
use stdClass;

return describe(FunctionRunner::class, function () {
    context('when running', function () {
        given('callback', function () { return function (stdClass $class) { return gettype($class); }; });
        when('result', function (FunctionRunner $that, Context $context, Prophet $prophet, $callback) {
            return $that->run($callback, $context->reveal(), $prophet->reveal());
        });
        then(function ($result) { return $result === 'NULL'; });
    });

    context('when building parameters', function () {
        given('prophecy', function () { return 'ProphecyReturnedByProphet'; });
        given('callback', function () { return function (stdClass $class) { }; });
        given(function (Context $context) { $context->hasCompiledValue('class')->willReturn(false); });
        given(function (Context $context) { $context->hasValue('class')->willReturn(false); });
        given(function (Context $context) { $context->hasValue('class')->willReturn(false); });
        given(function (Prophet $prophet, $prophecy) { $prophet->prophesize('stdClass')->willReturn($prophecy); });
        given(function (Context $context, $prophecy) {
            $context->addCompiledValue('class', $prophecy)->willReturn($prophecy)->shouldBeCalled();
        });

        when('result', function (FunctionRunner $that, Context $context, Prophet $prophet, $callback) {
            return $that->buildParameters($callback, $context->reveal(), $prophet->reveal());
        });

        then(function ($result, $prophecy) {
            return $result === [ $prophecy ];
        });
    });


});
