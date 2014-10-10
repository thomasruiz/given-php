<?php

use GivenPHP\Reporting\DefaultReporter;
use Mockery as m;

class MockError {
    function render($num, $label) {
        echo 'rendered error: ' . $num . ' ' . $label;
    }
    function summary() {
        echo 'error summary';
    }
}

describe('The default test reporter', function() {

    context('calling reportStart', function () {

        given('a version number', 'version', '0.1.0');
        given('an instance of the default reporter', 'reporter', new DefaultReporter());

        when('reporter #reportStart method is called', 'result', function ($reporter, $version) {
            ob_start();
            $reporter->reportStart($version);
            return ob_get_clean();
        });

        then('result should be a valid string', function ($result) {
            return !(false === strpos($result, 'GivenPHP v0.1.0'));
        });

    });

    context('calling #reportSuccess', function () {

        given('an instance of the default reporter', 'reporter', new DefaultReporter());
        given('count', 2);
        given('description', 'This test passes');
        
        when('reporter #reportSuccess is called', 'result', function ($reporter, $count, $description) {
            ob_start();
            $reporter->reportSuccess($count, $description);
            return ob_get_clean(); 
        });

        then('result should be a valid string', function ($result) {
            return !(false === strpos($result, '.'));
        });
    });

    context('calling #reportFailure', function () {

        given('an instance of the default reporter', 'reporter', new DefaultReporter());
        given('count', function () { return 2; });
        given('description', function () { return 'This test fails'; });
        
        when('reporter #reportSuccess is called', 'result', function ($reporter, $count, $description) {
            ob_start();
            $reporter->reportFailure($count, $description);
            return ob_get_clean(); 
        });

        then('result should be a valid string', function ($result) {
            return !(false === strpos($result, 'F'));
        });
    });

    describe('calling #reportEnd', function () {

        given('an instance of the default reporter', 'reporter', new DefaultReporter());

        context('a single test with no errors was executed', function () {

            given('total', 1);
            given('errors', array());
            given('labels', array());
            given('results', array());
            
            when('reporter #reportEnd is called', 'result', function (
                $reporter, $total, $errors, $labels, $results
            ) {
                ob_start();
                $reporter->reportEnd($total, $errors, $labels, $results);
                return ob_get_clean(); 
            });

            then('result should be a valid string', function ($result) {
                return !(false === strpos($result, '1 examples, 0 failures'));
            });
        });

        context('10 tests with 1 error were executed', function () {

            given('total', 10);
            given('errors', array(new MockError()));
            given('labels', array());
            given('results', array());
            
            when('reporter #reportEnd is called', 'result', function (
                $reporter, $total, $errors, $labels, $results
            ) {
                ob_start();
                $reporter->reportEnd($total, $errors, $labels, $results);
                return ob_get_clean(); 
            });

            then('result should be a valid string', function ($result) {
                return !(false === strpos($result, '10 examples, 1 failures'));
            });

            then('errors should have been rendered', function ($result) {
                return !(false === strpos($result, 'rendered error: 1'));
            });

            then('error summaries should have been rendered', function ($result) {
                return !(false === strpos($result, 'Failed examples:')) &&
                    !(false === strpos($result, 'error summary'));
            });
        });

        context('5 tests with 2 errors and labels were executed', function () {

            given('total', 5);
            given('errors', array(new MockError, new MockError));
            given('labels', array('Error number 1', 'Error number 2'));
            given('results', array());
            
            when('reporter #reportEnd is called', 'result', function (
                $reporter, $total, $errors, $labels, $results
            ) {
                ob_start();
                $reporter->reportEnd($total, $errors, $labels, $results);
                return ob_get_clean(); 
            });

            then('result should be a valid string', function ($result) {
                return !(false === strpos($result, '5 examples, 2 failures'));
            });

            then('errors should have been rendered', function ($result) {
                return !(false === strpos($result, 'rendered error: 1'))
                    && !(false === strpos($result, 'rendered error: 2'));
            });

            then('labels should have been rendered', function ($result) {
                return !(false === strpos($result, 'Error number 1'))
                    && !(false === strpos($result, 'Error number 2'));
            });

            then('error summaries should have been rendered', function ($result) {
                return !(false === strpos($result, 'Failed examples:'))
                    && !(false === strpos($result, 'error summary'));
            });
        });
        
    });
});
