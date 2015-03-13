<?php namespace GivenPHP\Formatters;

use GivenPHP\Container;
use GivenPHP\Events\ExampleEvent;
use GivenPHP\Events\SuiteEvent;
use GivenPHP\TestSuite\Context;
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
     * @var array
     */
    private $failedExamples = [ ];

    /**
     * Construct a new DotFormatter object
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input     = $input;
        $this->output    = $output;
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
            $this->failedExamples[] = [ $event->getExample(), $event->getContext(), $event->getResult() ];
        }
    }

    /**
     * @param SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event)
    {
        $time        = number_format($event->getTime() * 1000, 2, '.', '');
        $loadingTime = number_format($event->getLoadingTime() * 1000, 2, '.', '');

        $this->output->writeln("\n");
        $this->output->writeln("{$event->getTotalSpecifications()} specs");
        $this->output->writeln("{$event->getTotalExamples()} examples");
        $this->output->writeln("{$time} ms ({$loadingTime} loading)");
        $this->printFailedExamples();
    }

    private function printFailedExamples()
    {
        foreach ($this->failedExamples as $fail) {
            $this->printFailedExample($fail[1]);
        }
    }

    /**
     * @param Context $context
     */
    private function printFailedExample(Context $context)
    {
        $this->output->writeln("\n{$context->getContext()}");
    }

    /**
     * @return array
     */
    public function getFailedExamples()
    {
        return $this->failedExamples;
    }

    /**
     * @param array $failedExamples
     */
    public function setFailedExamples($failedExamples)
    {
        $this->failedExamples = $failedExamples;
    }
}