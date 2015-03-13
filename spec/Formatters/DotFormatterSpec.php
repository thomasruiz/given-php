<?php namespace spec\GivenPHP\Formatters;

use GivenPHP\Container;
use GivenPHP\Events\ExampleEvent;
use GivenPHP\Events\SuiteEvent;
use GivenPHP\Formatters\DotFormatter;
use GivenPHP\Formatters\Formatter;
use GivenPHP\TestSuite\Context;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return describe(DotFormatter::class, with('input', 'output'), function () {
    given('input', function (InputInterface $inputInterface) { return $inputInterface->reveal(); });
    given('output', function (OutputInterface $outputInterface) { return $outputInterface->reveal(); });

    then(function ($that) { return $that instanceof Formatter; });

    context('.afterExample', function () {
        when(function (DotFormatter $that, ExampleEvent $event) { $that->afterExample($event->reveal()); });

        context('with passing example', function () {
            given(function (ExampleEvent $event) { $event->getResult()->willReturn(true); });
            then(function (OutputInterface $outputInterface) { $outputInterface->write(".")->shouldHaveBeenCalled(); });
        });

        context('with failing example', function () {
            given(function (ExampleEvent $event) { $event->getResult()->willReturn(false); });
            given(function (ExampleEvent $event) { $event->getContext()->willReturn(null); });
            given(function (ExampleEvent $event) { $event->getExample()->willReturn(null); });
            then(function (OutputInterface $outputInterface) { $outputInterface->write("F")->shouldHaveBeenCalled(); });
        });
    });

    context('.afterSuite', function () {
        given(function (SuiteEvent $event) { $event->getTime()->willReturn(1); });
        given(function (SuiteEvent $event) { $event->getLoadingTime()->willReturn(.5); });
        given(function (SuiteEvent $event) { $event->getTotalSpecifications()->willReturn(2); });
        given(function (SuiteEvent $event) { $event->getTotalExamples()->willReturn(4); });
        given(function (Context $context) { $context->getContext()->willReturn('foo'); });
        given('example', function () { return function () { }; });
        given(function (DotFormatter $that, $example, Context $context) {
            $that->setFailedExamples([ [ $example, $context->reveal(), false ], [ $example, $context->reveal(), false ] ]);
        });

        when(function (DotFormatter $that, $event) { $that->afterSuite($event->reveal()); });

        then(function (OutputInterface $outputInterface) { $outputInterface->writeln("\n")->shouldBeCalled(); });
        then(function (OutputInterface $outputInterface) { $outputInterface->writeln("2 specs")->shouldBeCalled(); });
        then(function (OutputInterface $outputInterface) { $outputInterface->writeln("4 examples")->shouldBeCalled(); });
        then(function (OutputInterface $outputInterface) {
            $outputInterface->writeln("1000.00 ms (500.00 loading)")->shouldBeCalled();
        });
        then(function (OutputInterface $outputInterface) { $outputInterface->writeln("\nfoo")->shouldBeCalledTimes(2); });
    });
});