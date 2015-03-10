<?php
error_reporting(E_ALL);

use GivenPHP\Container;
use GivenPHP\GivenPHP;
use GivenPHP\Runners\FunctionRunner;
use GivenPHP\Runners\SpecRunner;

require 'vendor/autoload.php';

$container = new Container();
$container->define('testsuite.suite', '\GivenPHP\TestSuite\Suite');
$container->shared('runners.func', function () { return new FunctionRunner(); });
$container->shared('runners.spec', function () use ($container) {
    return new SpecRunner($container->shared('runners.func'));
});
$container->shared('givenphp', new GivenPHP($container, $container->build('testsuite.suite')));

$spec = require 'spec/TestSuite/SuiteSpec.php';
$spec->run();

$spec2 = require 'spec/TestSuite/SpecificationSpec.php';
$spec2->run();

$spec3 = require 'spec/TestSuite/ContextSpec.php';
$spec3->run();

$spec4 = require 'spec/Runners/FunctionRunnerSpec.php';
$spec4->run();

$spec5 = require 'spec/Runners/SpecRunnerSpec.php';
$spec5->run();

$spec6 = require 'spec/ContainerSpec.php';
$spec6->run();

$executer = $container->shared('runners.spec');
$executer->run($spec);
$executer->run($spec2);
$executer->run($spec3);
$executer->run($spec4);
$executer->run($spec5);
$executer->run($spec6);
