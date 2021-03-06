<?php namespace GivenPHP\Console;

use GivenPHP\Console\Commands\RunCommand;
use GivenPHP\Container;
use GivenPHP\Formatters\DotFormatter;
use GivenPHP\GivenPHP;
use GivenPHP\Runners\FunctionRunner;
use GivenPHP\Runners\SpecRunner;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends BaseApplication
{

    /**
     * Construct a new Application object
     *
     * @param string $version
     */
    public function __construct($version)
    {
        parent::__construct('GivenPHP', $version);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $container = new Container();

        $this->configureDI($container, $input, $output);

        $this->addCommands([
            new RunCommand($container)
        ]);

        return parent::doRun($input, $output);
    }

    /**
     * @param Container $container
     */
    private function configureDI(Container $container, InputInterface $input, OutputInterface $output)
    {
        $container->shared('input', $input);
        $container->shared('output', $output);

        $container->define('testsuite.suite', '\GivenPHP\TestSuite\Suite');
        $container->define('testsuite.spec', '\GivenPHP\TestSuite\Specification');
        $container->define('testsuite.context', '\GivenPHP\TestSuite\Context');

        $container->shared('givenphp', new GivenPHP($container, $container->build('testsuite.suite')));

        $container->shared('fs', function () use ($container) {
            $fs = new Filesystem($container->shared('fs.adapter'));

            return $fs->addPlugin($container->shared('fs.plugins.listfiles'));
        });

        $container->shared('fs.adapter', function () { return new Local(getcwd()); });
        $container->shared('fs.plugins.listfiles', function () { return new ListFiles(); });
        $container->shared('events', function () { return new EventDispatcher(); });

        $container->shared('runners.func', function () { return new FunctionRunner(); });
        $container->shared('runners.spec', function () use ($container) {
            return new SpecRunner($container->shared('runners.func'), $container->shared('events'));
        });

        $container->shared('formatter', function () use ($container) {
            return new DotFormatter($container->shared('input'), $container->shared('output'), $container->shared('fs'));
        });
    }
}