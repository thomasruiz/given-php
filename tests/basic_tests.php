<?php

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/utils.php';

describe('Natural assertions', function () {
    given('true', true);
    given('empty', [ ]);
    given('obj', new stdClass);

    then(function ($true) {
        return $true === true;
    });

    context('changing true to false', function () {
        when(function (&$true) {
            $true = false;
        });

        then(function ($true) {
            return $true === false;
        });
    });

    context('adding to the object', function () {
        when(function ($obj) {
            $obj->foo = 'bar';
        });

        then(function ($obj) {
            return property_exists($obj, 'foo') && $obj->foo === 'bar';
        });
    });

    context('inserting in the empty array', function () {
        when(function (&$empty) {
            $empty[] = 'OK';
        });

        then(function ($empty) {
            return !empty($empty);
        });

        then(function ($empty) {
            return count($empty) === 1;
        });
    });

    context('isolated', function () {
        then(function ($empty) {
            return empty($empty);
        });
    });
});

describe('Natural failing assertions', function () {
    given('foo', 1);
    given('expected', 2);

    then(function ($foo, $expected) {
        return $foo + $foo + 2 * $foo === $expected;
    });

    then(function () {
        return null == "HI" && true && 1;
    });

    then(function ($foo) {
        return $foo != 1;
    });
});
