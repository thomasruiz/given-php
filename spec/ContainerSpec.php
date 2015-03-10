<?php namespace spec\GivenPHP;

use GivenPHP\Container;
use stdClass;

return describe(Container::class, function () {
    context("when adding a shared instance", function () {
        context("via callback", function () {
            when(function (Container $that) { $that->shared('foo', function () { return new stdClass(); }); });
            then(function (Container $that) { return $that->shared('foo') instanceof stdClass; });
        });

        context('via direct instance', function () {
            when(function (Container $that) { $that->shared('foo', new stdClass()); });
            then(function (Container $that) { return $that->shared('foo') instanceof stdClass; });
        });
    });

    context("when building an object", function () {
        given(function (Container $that) { $that->define('foo', '\stdClass'); });
        when('fooInstance', function (Container $that) { return $that->build('foo'); });
        then(function ($fooInstance) { return $fooInstance instanceof stdClass; });
    });
});