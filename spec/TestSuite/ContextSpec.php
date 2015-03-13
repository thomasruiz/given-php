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

    context('when adding', function () {
        given('data', function () { return function () { }; });

        context('values', function () {
            when(function (Context $that, $data) { $that->addValue('data', $data); });
            then(function (Context $that) { return $that->hasValue('data'); });
            then(function (Context $that, $data) { return $that->getValue('data') === $data; });
        });

        context('modifiers', function () {
            when(function (Context $that, $data) { $that->addModifier($data); });
            when(function (Context $that, $data) { $that->addModifier($data); });
            then(function (Context $that, $data) { return $that->getModifiers() === [ $data, $data ]; });
        });

        context('let callbacks', function () {
            when(function (Context $that, $data) { $that->addLetCallback($data); });
            when(function (Context $that, $data) { $that->addLetCallback($data); });
            then(function (Context $that, $data) { return $that->getLetCallbacks() === [ $data, $data ]; });
        });

        context('examples', function () {
            when(function (Context $that, $data) { $that->addExample($data); });
            when(function (Context $that, $data) { $that->addExample($data); });
            then(function (Context $that, $data) { return $that->getExamples() === [ $data, $data ]; });
            then(function (Context $that) { return count($that) === 2; });
        });

        context('actions', function () {
            when(function (Context $that, $data) { $that->addActionWithResult('data', $data); });
            when(function (Context $that, $data) { $that->addActionWithoutResult($data); });
            then(function (Context $that, $data) { return $that->getActions() === [ 'data' => $data, $data ]; });
        });
    });
});