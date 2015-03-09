<?php namespace spec\GivenPHP\TestSuite;

use GivenPHP\TestSuite\Specification;
use GivenPHP\TestSuite\Suite;

return describe(Suite::class, function () {
    given(function (Specification $spec) { $spec->count()->willReturn(3); });

    context('adding specs', function () {
        given(function (Suite $that, Specification $spec) { $spec->setSuite($that)->shouldBeCalled(); });

        when(function (Suite $that, Specification $spec) { $that->addSpecification($spec->reveal()); });
        when(function (Suite $that, Specification $spec) { $that->addSpecification($spec->reveal()); });
        when(function (Suite $that, Specification $spec) { $that->addSpecification($spec->reveal()); });

        then(function (Suite $that, Specification $spec) {
            return $that->getSpecifications() === [ $spec->reveal(), $spec->reveal(), $spec->reveal() ];
        });

        then(function (Suite $that) { return $that->count() === 9; });
    });
});