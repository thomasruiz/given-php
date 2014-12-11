<?php

use GivenPHP\Console\Command\RunCommand;
use Symfony\Component\Console\Tester\CommandTester;

return describe('RunCommand', function () {
    given('runCommand', function () { return new RunCommand(); });
    given('commandTester', function (RunCommand $runCommand) { return new CommandTester($runCommand); });

    when('result', function (CommandTester $commandTester) { return $commandTester->execute([ ]); });

    then(function ($result) { return $result === 0; });
});
