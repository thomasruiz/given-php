<?php

require 'Stack.php';

describe('Stack', function () {
    Given('stack', function ($initial_contents) {
        return new Stack($initial_contents);
    });

    context('with no items', function () {
        given('initial_contents', function () {
            return [ ];
        });

        when(function (Stack $stack) {
            $stack->push(3);
        });

        then(function (Stack $stack) {
            return $stack->size() === 1;
        });
    });

    context('with one item', function () {
        given('initial_contents', function () {
            return [ 'an item' ];
        });

        when(function (Stack $stack) {
            $stack->push('another item');
        });

        then(function (Stack $stack) {
            return $stack->size() === 2;
        });
    });
});
