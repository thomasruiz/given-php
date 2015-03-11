<?php namespace GivenPHP\Formatters;

use GivenPHP\Events\ExampleEvent;
use GivenPHP\Events\SuiteEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DotFormatter extends Formatter
{

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Construct a new DotFormatter object
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param ExampleEvent $event
     */
    public function afterExample(ExampleEvent $event)
    {
        if ($event->getResult()) {
            $this->output->write('.');
        } else {
            $this->output->write('F');
        }
    }

    /**
     * @param SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event)
    {
        $this->output->writeln("\n");
        $this->output->writeln("{$event->getTotalSpecifications()} specs");
        $this->output->writeln("{$event->getTotalExamples()} examples");
        $this->output->writeln("{$event->getTime()} ms ({$event->getLoadingTime()} loading)");
    }
}