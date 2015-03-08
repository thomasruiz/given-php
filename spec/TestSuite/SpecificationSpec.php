<?php namespace spec\GivenPHP\TestSuite;

use GivenPHP\TestSuite\Context;
use GivenPHP\TestSuite\Specification;
use GivenPHP\TestSuite\Suite;

return describe(Specification::class, with('className', 'params', 'context'), function () {
    given('className', function () { return 'Foo'; });
    given('params', function () { return [ 'Bar' ]; });
    given('context', function (Context $contextProphecy) { return $contextProphecy->reveal(); });

    then(function (Specification $that, $className) { return $that->getTitle() === $className; });
    then(function (Specification $that, $params) { return $that->getConstructorParameters() === $params; });
    then(function (Specification $that) { return count($that->getContexts()) === 1; });
    then(function (Specification $that, $context) { return $that->getCurrentContext() === $context; });
    then(function (Specification $that, $context) { return $that->getContexts()[0] === $context; });

    context('when setting Suite', function () {
        when(function (Specification $that, Suite $suite) { $that->setSuite($suite); });
        then(function (Specification $that, Suite $suite) { return $that->getSuite() === $suite; });
    });

    context('when adding Contexts', function () {
        given(function (Context $contextProphecy) { $contextProphecy->count()->willReturn(8); });
        given(function (Context $contextProphecy) { $contextProphecy->run()->shouldBeCalled(); });
        when(function (Specification $that, $context) { $that->addContext($context); });
        when(function (Specification $that, $context) { $that->addContext($context); });
        then(function (Specification $that) { return count($that) === 24; });
    });

    context('when running', function () {
        given(function (Specification $that, Context $newContext) { $that->addContext($newContext->reveal()); });
        when(function (Specification $that) { $that->run(); });
        then(function (Context $contextProphecy) { $contextProphecy->run()->shouldHaveBeenCalled(); });
    });

    context('when calling undefined methods', function () {
        given('name', function () { return 'foo'; });
        given('value', function () { return function () { }; });
        when(function (Specification $that, $name, $value) { $that->addValue($name, $value); });
        then(function (Context $contextProphecy, $name, $value) {
            $contextProphecy->addValue($name, $value)
                            ->shouldHaveBeenCalled();
        });
    });
});