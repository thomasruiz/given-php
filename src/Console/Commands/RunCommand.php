<?php namespace GivenPHP\Console\Commands;

use GivenPHP\Container;
use GivenPHP\TestSuite\Specification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{

    /**
     * @var Container
     */
    private $container;

    /**
     * Construct a new RunCommand object
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('run')
             ->setDefinition([
                 new InputArgument('paths', InputArgument::IS_ARRAY, 'Specs to run', [ 'spec' ])
             ])
             ->setDescription('Run specs.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files  = $this->container->shared('fs');
        $result = $this->runSpecs($this->findSpecs($files->listFiles('spec', true)));

        return $result ? 0 : -1;
    }

    /**
     * @param array $files
     *
     * @return Specification[]
     */
    private function findSpecs(array $files)
    {
        $specs = [ ];

        foreach ($files as $file) {
            if (strrpos($file['basename'], 'Spec.php') === strlen($file['basename']) - strlen('Spec.php')) {
                $spec = require $file['path'];
                $spec->run();
                $specs[] = $spec;
            }
        }

        return $specs;
    }

    /**
     * @param Specification[] $specs
     *
     * @return bool|null
     */
    private function runSpecs(array $specs)
    {
        $specRunner = $this->container->shared('runners.spec');
        $result     = null;

        foreach ($specs as $spec) {
            $specResult = $specRunner->run($spec);

            $result = $result === null ? $specResult : $result && $specResult;
        }

        return $result;
    }
}