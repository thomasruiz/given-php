<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/utils.php';
require 'Stack.php';

describe('Stack', function () {
    Given('stack', function ($initial_contents) {
        return new Stack($initial_contents);
    });

    context('with no items', function () {
        Given('initial_contents', function () {
            return [ ];
        });

        When(function (Stack $stack) {
            $stack->push(3);
        });

        Then(function (Stack $stack) {
            return $stack->size() === 1;
        });
    });

    context('with one item', function () {
        Given('initial_contents', function () {
            return [ 'an item' ];
        });

        When(function (Stack $stack) {
            $stack->push('another item');
        });

        Then(function (Stack $stack) {
            return $stack->size() === 2;
        });
    });
});
