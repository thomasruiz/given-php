<?php

use GivenPHP\GivenPHP;
use Mockery as m;

describe('GivenPHP', function () {

    tearDown(function () {
        m::close();
    });

    given('testSuiteClass', function () {
        return m::mock('TestSuite');
    });

    given('testCaseClass', function () {
        return m::mock('TestCase');
    });

    given('failureClass', function () {
        return m::mock('Failure');
    });

    given('givenPhp', function ($testSuiteClass, $testCaseClass, $failureClass) {
        return new GivenPHP($testSuiteClass, $testCaseClass, $failureClass);
    });

    context('describe', function () {
        given('testSuiteClass', function () {
            $mock = m::mock('GivenPHP\TestSuite');
            $mock->shouldReceive('run')->once();

            return $mock;
        });

        when('rc', function (GivenPHP $givenPhp) {
            return $givenPhp->describe(null, null);
        });

        then(function ($rc, $testSuiteClass) {
            return $rc === $testSuiteClass;
        });
    });

    context('context', function () {
        context('within describe', function () {
            given('testSuiteClass', function () {
                $mock = m::mock('GivenPHP\TestSuite');
                $mock->shouldReceive('run')->once();
                $mock->shouldReceive('isolateContext')->with(null, null)->once()->andReturn(true);

                return $mock;
            });

            when(function (GivenPHP $givenPhp) {
                $givenPhp->describe(null, null);
            });

            when('rc', function (GivenPHP $givenPhp) {
                return $givenPhp->context(null, null);
            });

            then(function ($rc) {
                return $rc === true;
            });
        });

        context('outside describe', function () {
            given('testSuiteClass', function () {
                $mock = m::mock('GivenPHP\TestSuite');
                $mock->shouldReceive('isolateContext')->with(null, null)->once()->andReturn(true);

                return $mock;
            });

            when('rc', function (GivenPHP $givenPhp) {
                return $givenPhp->context(null, null);
            });

            then(failsWith('BadFunctionCallException'));
        });
    });

    context('given', function () {
        context('within describe', function () {
            given('testSuiteClass', function () {
                $mock = m::mock('GivenPHP\TestSuite');
                $mock->shouldReceive('run')->once();
                $mock->shouldReceive('addUncompiledValue')->with(null, null)->once()->andReturn(true);

                return $mock;
            });

            when(function (GivenPHP $givenPhp) {
                $givenPhp->describe(null, null);
            });

            when('rc', function (GivenPHP $givenPhp) {
                return $givenPhp->given(null, null);
            });

            then(function ($rc) {
                return $rc === true;
            });
        });

        context('outside describe', function () {
            given('testSuiteClass', function () {
                $mock = m::mock('GivenPHP\TestSuite');
                $mock->shouldReceive('addUncompiledValue')->with(null, null)->once()->andReturn(true);

                return $mock;
            });

            when('rc', function (GivenPHP $givenPhp) {
                return $givenPhp->given(null, null);
            });

            then(failsWith('BadFunctionCallException'));
        });
    });

    context('when', function () {
        context('within describe', function () {
            given('testSuiteClass', function () {
                $mock = m::mock('GivenPHP\TestSuite');
                $mock->shouldReceive('run')->once();
                $mock->shouldReceive('addAction')->with(null, null)->once()->andReturn(true);

                return $mock;
            });

            when(function (GivenPHP $givenPhp) {
                $givenPhp->describe(null, null);
            });

            when('rc', function (GivenPHP $givenPhp) {
                return $givenPhp->when(null, null);
            });

            then(function ($rc) {
                return $rc === true;
            });
        });

        context('outside describe', function () {
            given('testSuiteClass', function () {
                $mock = m::mock('GivenPHP\TestSuite');
                $mock->shouldReceive('addAction')->with(null, null)->once()->andReturn(true);

                return $mock;
            });

            when('rc', function (GivenPHP $givenPhp) {
                return $givenPhp->when(null, null);
            });

            then(failsWith('BadFunctionCallException'));
        });
    });
});
