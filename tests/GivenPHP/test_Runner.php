<?php

use GivenPHP\Runner;

describe('Runner', function() {
    context('with no command line arguments', function() {
        $_SERVER['argv'] = [''];

        given('runner', function() {
            return new Runner();
        });

        when(function(Runner $runner) {
            $runner->run(false);
        });

        then(function($runner) {
            return $runner->isDone() === true;
        });

        then(function($runner) {
            return count($runner->getFilesExecuted()) === 0;
        });
    });
});
