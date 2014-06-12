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
        given('given_php', m::mock()->shouldReceive('get_value')->andReturn('foo')->getMock());
        given('callback', new EnhancedCallback(function ($foo_var) {
            return $foo_var;
        }));

        then(function ($callback, $given_php) {
            $result = $callback($given_php) === 'foo';
            m::close();
            return $result;
        });
    });

    context('parameters', function () {
        given('given_php', m::mock()->shouldReceive('get_value')->twice()->andReturn(1)->getMock());
        given('callback', new EnhancedCallback(function ($param_1, $param_2) {
            return $param_1 + $param_2;
        }));

        when('parameters', function ($given_php, EnhancedCallback $callback) {
            return $callback->parameters($given_php);
        });

        then(function ($parameters) {
            return count($parameters) === 4;
        });

        then(function ($parameters) {
            return isset($parameters[0], $parameters[1], $parameters['param_1'], $parameters['param_2']);
        });
    });
});
