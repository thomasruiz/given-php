<?php namespace GivenPHP\Console\Commands;

use GivenPHP\Container;
use GivenPHP\Events\SuiteEvent;
use GivenPHP\Runners\SpecRunner;
use GivenPHP\TestSuite\Specification;
use GivenPHP\TestSuite\Suite;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        $this->setName('run')->setDefinition([
            new InputArgument('paths', InputArgument::IS_ARRAY, 'Specs to run', [ 'spec' ])
        ])->setDescription('Run specs.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $events = $this->container->shared('events');
        $suite  = $this->container->shared('givenphp')->getSuite();

        $this->prepare($suite, $events);
        $files      = $this->container->shared('fs');
        $specRunner = $this->container->shared('runners.spec');
        $result     = $this->runSpecs($this->findSpecs($files->listFiles('spec', true)), $specRunner);
        $events->dispatch('afterSuite', new SuiteEvent($suite));

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

        $this->container->shared('givenphp')->getSuite()->setLoadingEndTime(microtime(true));

        return $specs;
    }

    /**
     * @param Specification[] $specs
     * @param SpecRunner      $specRunner
     *
     * @return bool|null
     */
    private function runSpecs(array $specs, SpecRunner $specRunner)
    {
        $result = null;

        foreach ($specs as $spec) {
            $specResult = $specRunner->run($spec);

            $result = $result === null ? $specResult : $result && $specResult;
        }

        $this->container->shared('givenphp')->getSuite()->setEndTime(microtime(true));

        return $result;
    }

    /**
     * @param Suite                    $suite
     * @param EventDispatcherInterface $events
     */
    protected function prepare(Suite $suite, EventDispatcherInterface $events)
    {
        $formatter = $this->container->shared('formatter');
        foreach ([ 'beforeSuite', 'afterSuite', 'beforeSpec', 'afterSpec', 'beforeExample', 'afterExample' ] as $event) {
            $events->addListener($event, [ $formatter, $event ]);
        }

        $suite->setStartTime(microtime(true));
        $suite->setLoadingStartTime(microtime(true));

        $events->dispatch('beforeSuite', new SuiteEvent($suite));
    }
}