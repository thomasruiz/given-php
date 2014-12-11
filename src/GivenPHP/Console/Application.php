<?php namespace GivenPHP\Console;

use GivenPHP\Console\Command\RunCommand;
use GivenPHP\Runner\DefaultRunner;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    /**
     * Construct a new Application object.
     *
     * @param string $version
     */
    public function __construct($version = 'dev')
    {
        parent::__construct('GivenPHP', $version);
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->add(new RunCommand(new DefaultRunner()));

        return parent::doRun($input, $output);
    }
}
