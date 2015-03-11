<?php namespace spec\GivenPHP\Formatters;

use GivenPHP\Events\ExampleEvent;
use GivenPHP\Formatters\DotFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return describe(DotFormatter::class, with('input', 'output'), function () {
    given('input', function (InputInterface $inputInterface) { return $inputInterface->reveal(); });
    given('output', function (OutputInterface $outputInterface) { return $outputInterface->reveal(); });

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
});