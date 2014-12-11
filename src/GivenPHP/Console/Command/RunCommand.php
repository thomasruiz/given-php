<?php namespace GivenPHP\Console\Command;

use GivenPHP\Runner\Contracts\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{

    /**
     * Dependency injection of the Runner.
     *
     * @var Runner
     */
    private $runner;

    /**
     * Construct a new RunCommand object.
     *
     * @param Runner $runner
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
        parent::__construct();
    }

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
        return $this->runner->run($input->getArgument('paths'));
    }
}
