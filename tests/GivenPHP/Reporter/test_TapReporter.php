<?php

use GivenPHP\Reporting\TapReporter;

describe('The tap test reporter', function() {

    given('an instance of the tap reporter', 'reporter', new TapReporter());

    context('calling reportStart', function () {

        given('a version number', 'version', '0.1.0');
        
        when('reporter #reportStart method is called', 'result', function ($reporter, $version) {
            ob_start();
            $reporter->reportStart($version);
            return ob_get_clean();
        });

        then('result should be empty', function ($result) {
            return empty($result);
        });

    });

    context('calling #reportSuccess', function () {

        given('count', 2);
        given('description', 'This test passes');
        
        when('reporter #reportSuccess is called', 'result', function ($reporter, $count, $description) {
            ob_start();
            $reporter->reportSuccess($count, $description);
            return ob_get_clean(); 
        });

        then('result should be a valid string', function ($result) {
            return !(false === strpos($result, 'ok 2 This test passes'));
        });
    });

    context('calling #reportFailure', function () {

        given('count', 3);
        given('description', 'This test fails');
        
        when('reporter #reportSuccess is called', 'result', function ($reporter, $count, $description) {
            ob_start();
            $reporter->reportFailure($count, $description);
            return ob_get_clean(); 
        });

        then('result should be a valid string', function ($result) {
            return !(false === strpos($result, 'not ok 3 This test fails'));
        });
    });

    describe('calling #reportEnd', function () {

        given('errors', array());
        given('labels', array());
        given('results', array());
        given('an instance of the tap reporter', 'reporter', new TapReporter());

        context('no tests were executed', function () {
        
            given('total', 0);

            when('reporter #reportEnd is called', 'result', function (
                $reporter, $total, $errors, $labels, $results
            ) {
                ob_start();
                $reporter->reportEnd($total, $errors, $labels, $results);
                return ob_get_clean(); 
            });

            then('result should be a valid string', function ($result) {
                return empty($result);
            });
        });

        context('11 tests with no errors was executed', function () {

            given('total', 11);

            when('reporter #reportEnd is called', 'result', function (
                $reporter, $total, $errors, $labels, $results
            ) {
                ob_start();
                $reporter->reportEnd($total, $errors, $labels, $results);
                return ob_get_clean(); 
            });

            then('result should be a valid string', function ($result) {
                return !(false === strpos($result, '1..11'));
            });
        });
    });

});
