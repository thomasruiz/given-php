<?php

use GivenPHP\TestSuite;
use Mockery as m;

describe('TestSuite', function () {

    tearDown(function() {
        m::close();
    });

    given('callback', null);

    given('suite', function ($callback, $contextMock) {
        return new TestSuite('simple suite', $callback, $contextMock);
    });

    context('proxy context', function () {
        given('contextMock', function () {
            $mock = m::mock('TestContext');

            $mock->shouldReceive('reset')->once()->withNoArgs();
            $mock->shouldReceive('executeActions')->once()->withNoArgs();
            $mock->shouldReceive('addUncompiledValue')->once()->with('foo', 'bar');
            $mock->shouldReceive('addAction')->once()->with('foo', null);
            $mock->shouldReceive('addTearDownAction')->once()->with(null);
            $mock->shouldReceive('addSetUpAction')->once()->with(null);
            $mock->shouldReceive('executeCallback')->once()->with(null);
            $mock->shouldReceive('tearDown')->once()->with();
            $mock->shouldReceive('setUp')->once()->with();
            $mock->shouldReceive('run')->once()->andReturn(true);

            return $mock;
        });

        when(function (TestSuite $suite) {
            $suite->reset();
        });

        when(function (TestSuite $suite) {
            $suite->executeActions();
        });

        when(function (TestSuite $suite) {
            $suite->addUncompiledValue('foo', 'bar');
        });

        when(function (TestSuite $suite) {
            $suite->addAction('foo', null);
        });

        when(function (TestSuite $suite) {
            $suite->addTearDownAction(null);
        });

        when(function (TestSuite $suite) {
            $suite->addSetUpAction(null);
        });

        when(function (TestSuite $suite) {
            $suite->executeCallback(null);
        });

        when(function (TestSuite $suite) {
            $suite->tearDown();
        });

        when(function (TestSuite $suite) {
            $suite->setUp();
        });

        when('rc', function (TestSuite $suite) {
            return $suite->run();
        });

        then(function ($rc) {
            return $rc === true;
        });
    });

    context('creating a subcontext', function () {
        given('contextMock', function () {
            $mock = m::mock('TestContext');

            $mock->shouldReceive('addParentContext')->once()->with($mock);
            $mock->shouldReceive('run')->once()->withAnyArgs()->andReturn(123);

            return $mock;
        });

        when('rc', function (TestSuite $suite, $contextMock) {
            return $suite->isolateContext($contextMock, null);
        });

        then(function ($rc) {
            return $rc === 123;
        });
    });
});
