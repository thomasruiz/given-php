<?php

use GivenPHP\TestContext;
use Mockery as m;

describe('TestContext', function () {

    tearDown(function () {
        m::close();
    });

    given('enhancedCallback', function () {
        return m::mock('EnhancedCallback');
    });

    given('context', function ($enhancedCallback) {
        return new TestContext('simple context', function () { return '1234'; }, $enhancedCallback);
    });

    then(function (TestContext $context) {
        return $context->getLabel() === 'simple context';
    });

    context('simple run', function () {
        when('rc', function (TestContext $context) {
            return $context->run();
        });

        then(function ($rc) {
            return $rc === '1234';
        });
    });

    context('uncompiled values', function () {
        context('are assigned correctly', function () {
            when(function (TestContext $context) {
                $context->addUncompiledValue('foo', 'bar');
            });

            then(function (TestContext $context) {
                return $context->getUncompiledValue('foo') === 'bar';
            });

            context('with reassignments', function () {
                when(function (TestContext $context) {
                    $context->addUncompiledValue('foo', 'foobar');
                });

                then(function (TestContext $context) {
                    return $context->getUncompiledValue('foo') === 'foobar';
                });
            });
        });

        context('when not assigned', function () {
            when(function (TestContext $context) {
                $context->getUncompiledValue('unexisting');
            });

            then(failsWith('UnexpectedValueException'));
        });
    });

    context('compiled values', function () {
        given('enhancedCallback', function () {
            $enhancedCallback = m::mock('EnhancedCallback');
            $enhancedCallback->shouldReceive('__invoke')->andReturn('bar');

            return $enhancedCallback;
        });

        when(function (TestContext $context) {
            $context->addUncompiledValue('foo', 'foo');
        });

        when(function (TestContext $context) {
            $context->addUncompiledValue('bar', function () {
                return 'bar';
            });
        });

        then(function (TestContext $context) {
            return $context->getValue('foo') === 'foo';
        });

        then(function (TestContext $context) {
            return $context->getValue('bar') === 'bar';
        });
    });

    context('with a parent', function () {
        given('parentContext', function ($enhancedCallback) {
            $context = new TestContext('parent context', function () { return '1234'; }, $enhancedCallback);

            $context->addUncompiledValue('foo', 'foo');
            $context->addUncompiledValue('bar', 'bar');
            $context->addAction('simpleAction', null);
            $context->addSetUpAction(null);
            $context->addTearDownAction(null);

            return $context;
        });

        when(function (TestContext $context, $parentContext) {
            $context->addParentContext($parentContext);
        });

        then(function (TestContext $context) {
            return $context->getLabel() === 'parent context simple context';
        });

        then(function (TestContext $context) {
            return $context->getUncompiledValue('foo') === 'foo';
        });

        then(function (TestContext $context) {
            return $context->getValue('bar') === 'bar';
        });

        then(function (TestContext $context) {
            return count($context->getActions()) === 1;
        });

        then(function (TestContext $context) {
            return count($context->getTearDownActions()) === 1;
        });

        then(function (TestContext $context) {
            return count($context->getSetUpActions()) === 1;
        });
    });
});
