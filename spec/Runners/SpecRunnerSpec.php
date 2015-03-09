<?php namespace spec\GivenPHP\Runners;

use GivenPHP\Runners\FunctionRunner;
use GivenPHP\Runners\SpecRunner;
use GivenPHP\TestSuite\Context;
use GivenPHP\TestSuite\Specification;
use Prophecy\Argument;

return describe(SpecRunner::class, with('functionRunner'), function () {
    given('functionRunner', function (FunctionRunner $functionRunnerProphecy) { return $functionRunnerProphecy->reveal(); });
    given('callback', function () { return function () { }; });

    given(function (Context $context) { $context->getContext()->willReturn("test in progress"); });
    given(function (Context $context) { $context->getCompiledValues()->willReturn([ ]); });
    given(function (Context $context) { $context->getActions()->willReturn([ ]); });
    given(function (Context $context) { $context->getModifiers()->willReturn([ ]); });
    given(function (Context $context) { $context->getLetCallbacks()->willReturn([ ]); });
    given(function (Context $context, $callback) { $context->getExamples()->willReturn([ $callback ]); });
    given(function (Specification $spec, Context $context) { $spec->getContexts()->willReturn([ $context->reveal() ]); });
    given(function (Specification $spec) { $spec->getTitle()->willReturn('stdClass'); });
    given(function (Specification $spec) { $spec->getConstructorParameters()->willReturn([ ]); });
    given(function (Context $context) { $context->addCompiledValue('that', Argument::type('stdClass'))->shouldBeCalled(); });

    context('when running test', function () {
        when('result', function (SpecRunner $that, Specification $spec) { return $that->run($spec->reveal()); });

        context('that passes', function () {
            given(function (FunctionRunner $functionRunnerProphecy, $callback) {
                $functionRunnerProphecy->run($callback, Argument::type('\GivenPHP\TestSuite\Context'),
                    Argument::type('\Prophecy\Prophet'))->willReturn(true)->shouldBeCalled();
            });

            then(function ($result) { return $result === true; });
        });

        context('that fails', function () {
            given(function (FunctionRunner $functionRunnerProphecy, $callback) {
                $functionRunnerProphecy->run($callback, Argument::type('\GivenPHP\TestSuite\Context'),
                    Argument::type('\Prophecy\Prophet'))->willReturn(false)->shouldBeCalled();
            });

            then(function ($result) { return $result === false; });
        });
    });
});