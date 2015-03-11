<?php namespace spec\GivenPHP\Formatters;

use GivenPHP\Events\ExampleEvent;
use GivenPHP\Events\SuiteEvent;
use GivenPHP\Formatters\DotFormatter;
use GivenPHP\Formatters\Formatter;
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
            then(function (OutputInterface $outputInterface) { $outputInterface->write("F")->shouldHaveBeenCalled(); });
        });
    });

    context('.afterSuite', function () {
        given(function (SuiteEvent $event) { $event->getTime()->willReturn(1); });
        given(function (SuiteEvent $event) { $event->getLoadingTime()->willReturn(.5); });
        given(function (SuiteEvent $event) { $event->getTotalSpecifications()->willReturn(2); });
        given(function (SuiteEvent $event) { $event->getTotalExamples()->willReturn(4); });

        when(function (DotFormatter $that, $event) { $that->afterSuite($event->reveal()); });

        then(function (OutputInterface $outputInterface) { $outputInterface->writeln("\n")->shouldBeCalled(); });
        then(function (OutputInterface $outputInterface) { $outputInterface->writeln("2 specs")->shouldBeCalled(); });
        then(function (OutputInterface $outputInterface) {
            $outputInterface->writeln("4 examples")->shouldBeCalled();
        });
        then(function (OutputInterface $outputInterface) { $outputInterface->writeln("1 ms (0.5 loading)")->shouldBeCalled(); });
    });
});