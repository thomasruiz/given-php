<?php

use GivenPHP\TestCase;
use GivenPHP\TestResult;
use GivenPHP\TestSuite;
use Mockery as m;

describe('TestCase', function () {
    tearDown(function () {
        m::close();
    });

    given('test', null);

    given('suite', function ($test) {
        $mock = m::mock('GivenPHP\TestSuite');
        $mock->shouldReceive('reset')->once();
        $mock->shouldReceive('executeActions')->once();
        $mock->shouldReceive('executeCallback')->once()->with($test)->andReturn($test);
        $mock->shouldReceive('tearDown')->once();

        return $mock;
    });

    given('testCase', function ($test) {
        return new TestCase($test);
    });

    when('rc', function (TestCase $testCase, TestSuite $suite) {
        return $testCase->run($suite);
    });

    then(function ($rc) {
        return $rc instanceof TestResult;
    });

    then(function (TestResult $rc, $testCase) {
        return $rc->getTestCase() === $testCase;
    });

    then(function (TestResult $rc, $suite) {
        return $rc->getSuite() === $suite;
    });

    context('passing test', function () {
        given('test', true);

        then(function (TestResult $rc) {
            return $rc->isError() === false;
        });
    });

    context('failing test', function() {
        given('test', false);

        then(function (TestResult $rc) {
            return $rc->isError() === true;
        });
    });
});
