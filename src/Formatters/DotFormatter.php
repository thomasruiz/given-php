<?php namespace GivenPHP\Formatters;

use Exception;
use GivenPHP\Events\ExampleEvent;
use GivenPHP\Events\SuiteEvent;
use GivenPHP\TestSuite\Context;
use League\Flysystem\Filesystem;
use ReflectionFunction;
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
     * @var Filesystem
     */
    private $files;

    /**
     * Construct a new DotFormatter object
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Filesystem      $files
     */
    public function __construct(InputInterface $input, OutputInterface $output, Filesystem $files)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->files  = $files;
    }

    /**
     * @param ExampleEvent $event
     */
    public function afterExample(ExampleEvent $event)
    {
        if ($event->getResult() === true) {
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
            $this->printFailedExample($fail[0], $fail[1], $fail[2]);
        }
    }

    /**
     * @param callable $test
     * @param Context  $context
     */
    private function printFailedExample(callable $test, Context $context, $error)
    {
        $code = $this->getFunctionCode($test);
        $this->output->writeln("\n{$context->getContext()}: {$code}");

        if ($error instanceof Exception) {
            $this->output->writeln($error->getMessage());
        }
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

    /**
     * @param callable $test
     *
     * @return string
     */
    private function getFunctionCode(callable $test)
    {
        $functionReflection = new ReflectionFunction($test);
        $file               = $this->files->read(str_replace(getcwd(), '', $functionReflection->getFileName()));
        $lines              = explode("\n", $file);
        $code               = '';
        for (
            $currentLine = $functionReflection->getStartLine(), $endLine = $functionReflection->getEndLine();
            $currentLine <= $endLine; $currentLine++
        ) {
            $code .= $lines[ $currentLine - 1 ];
        }

        $start = strpos($code, '{') + 1;
        $end   = strrpos($code, '}') - $start - 1;
        $code  = substr($code, $start, $end);
        $code  = trim(preg_replace('/return/', '', $code, 1), ";\t\n\r ");

        return $code;
    }
}