<?php

describe('Expections', function() {
    context('fails', function() {
        context('without argument', function() {
            when(function () {
                throw new Exception;
            });

            then(fails());
        });

        context('with argument', function() {
            when(function () {
                throw new InvalidArgumentException;
            });

            then(fails('InvalidArgumentException'));

            then(failsWith('InvalidArgumentException'));
        });
    });
});
