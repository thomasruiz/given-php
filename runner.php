<?php
error_reporting(E_ALL);

use GivenPHP\Runners\FunctionRunner;
use GivenPHP\Runners\SpecRunner;

require 'vendor/autoload.php';
$spec = require 'spec/TestSuite/SuiteSpec.php';
$spec->run();

$spec2 = require 'spec/TestSuite/SpecificationSpec.php';
$spec2->run();

$spec3 = require 'spec/TestSuite/ContextSpec.php';
$spec3->run();

$executer = new SpecRunner(new FunctionRunner());
$executer->run($spec);
$executer->run($spec2);
$executer->run($spec3);
