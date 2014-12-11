<?php

use GivenPHP\Console\Application;
use GivenPHP\Console\Command\RunCommand;
use GivenPHP\Runner\DefaultRunner;
use Symfony\Component\Console\Tester\CommandTester;

return describe('RunCommand', function () {
    function mockRunner()
    {
        $mock = Mockery::mock('GivenPHP\Runner\DefaultRunner');
        $mock->shouldReceive('run')->once();

        return $mock;
    }

    given('runner', function () { return mockRunner(); });
    given('application', function () { return new Application(); });
    given('runCommand', function (DefaultRunner $runner) { return new RunCommand($runner); });
    given('commandTester', function (RunCommand $runCommand) { return new CommandTester($runCommand); });
    given('input', function ($paths) { return [ 'command' => 'run', 'paths' => $paths ]; });

    when(function (Application $application, RunCommand $runCommand) { $application->add($runCommand); });
    when('result', function (CommandTester $commandTester, $input) { return $commandTester->execute($input); });

    context('with no path', function () {
        given('paths', [ ]);

        then(function ($result) { return $result === 0; });
    });

    context('with one path', function () {
        given('paths', [ 'test.php' ]);

        then(function ($result) { return $result === 0; });
    });
});
