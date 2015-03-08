<?php namespace spec\GivenPHP\TestSuite;

use GivenPHP\TestSuite\Context;

return describe(Context::class, with('context', 'callback', 'parent'), function () {
    given('context', function () { return 'when foo'; });
    given('callback', function () { return function () { return 'callback called'; }; });
    given('parent', function (Context $parentContext) { return $parentContext->reveal(); });

    context('when building with a parent', function () {
        let(function (Context $parentContext) { $parentContext->getContext()->willReturn('context'); });
        let(function (Context $parentContext) { $parentContext->getValues()->willReturn('values'); });
        let(function (Context $parentContext) { $parentContext->getModifiers()->willReturn('modifiers'); });
        let(function (Context $parentContext) { $parentContext->getActions()->willReturn('actions'); });
        let(function (Context $parentContext) { $parentContext->getCompiledValues()->willReturn('compiled'); });
        then(function (Context $that) { return $that->getContext() === 'context when foo'; });
        then(function (Context $that) { return $that->getValues() === 'values'; });
        then(function (Context $that) { return $that->getModifiers() === 'modifiers'; });
        then(function (Context $that) { return $that->getActions() === 'actions'; });
        then(function (Context $that) { return $that->getCompiledValues() === 'compiled'; });
    });

    context('when running', function () {
        when('result', function (Context $that) { return $that->run(); });
        then(function ($result) { return $result === 'callback called'; });
    });
});