<?php

use GivenPHP\TestResult;
use Mockery as m;

describe('TestResult', function () {
    tearDown(function () {
        m::close();
    });

    given('result', function ($testRc, $suite, $testCase) {
        return new TestResult($testRc, $suite, $testCase);
    });

    given('testRc', null);

    given('suite', function () {
        return m::mock('GivenPHP\TestSuite');
    });

    given('testCase', function () {
        return m::mock('GivenPHP\TestCase');
    });

    then(function (TestResult $result, $suite) {
        return $result->getSuite() === $suite;
    });

    then(function (TestResult $result, $testCase) {
        return $result->getTestCase() === $testCase;
    });

    context('as a success', function () {
        given('testRc', true);

        then(function (TestResult $result) {
            return $result->isError() === false;
        });
    });

    context('as an error', function () {
        given('testRc', false);

        then(function (TestResult $result) {
            return $result->isError() === true;
        });
    });
});
