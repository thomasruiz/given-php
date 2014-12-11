<?php namespace GivenPHP\Console\Command;

use GivenPHP\Runner\DefaultRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run')
             ->setDefinition([
                 new InputArgument('paths', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Specs to run')
             ])
             ->setDescription('Run specs.');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = new DefaultRunner($input->getArgument('paths'));

        return $runner->run();
    }
}
