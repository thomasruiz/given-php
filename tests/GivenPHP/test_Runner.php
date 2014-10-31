<?php

use GivenPHP\Runner;
use Mockery as m;

describe('Runner', function () {

    tearDown(function () {
        m::close();
    });

    given('givenphp', m::mock('GivenPHP')->shouldReceive('setReporter')->once()->getMock());

    context('with no command line arguments', function () {
        $_SERVER['argv'] = [''];

        given('runner', function ($givenphp) {
            return new Runner($givenphp);
        });

        when(function (Runner $runner) {
            $runner->run(false);
        });

        then(function (Runner $runner) {
            return $runner->isDone() === true;
        });

        then(function (Runner $runner) {
            return count($runner->getFilesExecuted()) === 0;
        });
    });

    context('with files', function () {
        $_SERVER['argv'] = ['test_unexisting_file', 'tests/GivenPHP/stubs/test_simple.php', 'test_unexisting_file2'];

        given('runner', function ($givenphp) {
            return new Runner($givenphp);
        });

        when(function (Runner $runner) {
            $runner->run(false);
        });

        then(function (Runner $runner) {
            return $runner->isDone() === true;
        });

        then(function (Runner $runner) {
            return count($runner->getFilesExecuted()) === 1;
        });
    });

    context('valid options', function () {
        context('reporting', function () {
            $_SERVER['argv'] = ['tests/test_simple.php', '-r', 'null'];

            given('runner', function ($givenphp) {
                return new Runner($givenphp);
            });

            then(function (Runner $runner) {
                return $runner->getReporter() instanceof \GivenPHP\Reporting\NullReporter;
            });
        });
    });
});
