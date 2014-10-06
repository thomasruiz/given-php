<?php

describe('ErrorHandling', function() {
    context('Exceptions', function() {
        context('Basic exceptions', function() {
            when(function() {
                    throw new Exception();
            });

            then(fails());
            then(failsWith('Exception'));
        });

        context('Specialized exceptions', function() {
            class ExtendedException extends Exception {}

            when(function() {
                throw new ExtendedException;
            });

            then(failsWith('ExtendedException'));
            then(failsWith('Exception'));
        });
    });
});
