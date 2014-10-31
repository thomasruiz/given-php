<?php

describe('Simple tests', function () {
    given('foo', 'foo');

    when('foobar', function ($foo) {
        return $foo . 'bar';
    });

    then(function ($foobar) {
        return $foobar === 'foobar';
    });
});
