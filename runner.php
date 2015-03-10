<?php
error_reporting(E_ALL);

use GivenPHP\Container;
use GivenPHP\GivenPHP;
use GivenPHP\Runners\FunctionRunner;
use GivenPHP\Runners\SpecRunner;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;

require 'vendor/autoload.php';

$container = new Container();
$container->shared('fs', function () use ($container) {
    $fs = new Filesystem($container->shared('fs.local'));

    return $fs->addPlugin($container->shared('fs.plugins.listfiles'));
});
$container->shared('fs.local', function () { return new Local(getcwd()); });
$container->shared('fs.plugins.listfiles', function () { return new ListFiles(); });
$container->define('testsuite.suite', '\GivenPHP\TestSuite\Suite');
$container->define('testsuite.spec', '\GivenPHP\TestSuite\Specification');
$container->define('testsuite.context', '\GivenPHP\TestSuite\Context');
$container->shared('runners.func', function () { return new FunctionRunner(); });
$container->shared('runners.spec', function () use ($container) {
    return new SpecRunner($container->shared('runners.func'));
});
$container->shared('givenphp', new GivenPHP($container, $container->build('testsuite.suite')));

$specs = [ ];

foreach ($container->shared('fs')->listFiles('spec', true) as $file) {
    $spec = require $file['path'];
    $spec->run();
    $specs[] = $spec;
}

$executer = $container->shared('runners.spec');

foreach ($specs as $spec) {
    $executer->run($spec);
}
