<?php

require 'Stack.php';

describe('Stack', function () {
    given('stack', function ($initialContents) {
        return new Stack($initialContents);
    });

    context('with no items', function () {
        given('initialContents', function () {
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
        given('initialContents', function () {
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
