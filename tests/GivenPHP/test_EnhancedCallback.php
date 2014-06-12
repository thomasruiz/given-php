<?php

use GivenPHP\EnhancedCallback;
use Mockery as m;

describe('EnhancedCallback', function () {
    context('using a simple callback', function () {
        given('callback', new EnhancedCallback(function () {
            return 'foo';
        }));

        then(function ($callback) {
            return $callback() === 'foo';
        });
    });

    context('using a real context', function () {
        given('given_php', m::mock('GivenPHP')->shouldReceive('get_value')->andReturn('foo')->getMock());
        given('callback', new EnhancedCallback(function ($foo_var) {
            return $foo_var;
        }));

        then(function ($callback, $given_php) {
            $result = $callback($given_php) === 'foo';
            m::close();
            return $result;
        });
    });
});
