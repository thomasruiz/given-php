<?php namespace GivenPHP\Runners;

use Exception;
use GivenPHP\Events\ExampleEvent;
use GivenPHP\TestSuite\Context;
use GivenPHP\TestSuite\Specification;
use Prophecy\Prophet;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SpecRunner
{

    /**
     * @var FunctionRunner
     */
    private $functionRunner;

    /**
     * @var EventDispatcherInterface
     */
    private $events;

    /**
     * Construct a new SpecRunner object
     *
     * @param FunctionRunner           $exampleRunner
     * @param EventDispatcherInterface $events
     */
    public function __construct(FunctionRunner $exampleRunner, EventDispatcherInterface $events)
    {
        $this->functionRunner = $exampleRunner;
        $this->events         = $events;
    }

    /**
     * @param Specification $spec
     *
     * @return bool
     */
    public function run(Specification $spec)
    {
        $result = true;

        foreach ($spec->getContexts() as $context) {
            $result = $this->runExamples($context, $spec) && $result;
        }

        return $result;
    }

    /**
     * @param Context       $context
     * @param Specification $spec
     *
     * @return bool
     */
    private function runExamples(Context $context, Specification $spec)
    {
        $specResult = true;

        foreach ($context->getExamples() as $example) {
            $this->events->dispatch('beforeExample', new ExampleEvent($example, $context, null));

            try {
                $result = $this->runExample(clone $context, $example, $spec);
            } catch (Exception $e) {
                $result = $e;
            }

            $this->events->dispatch('afterExample', new ExampleEvent($example, $context, $result));

            if ($result === false || $result instanceof Exception) {
                $specResult = false;
            }
        }

        return $specResult;
    }

    /**
     * @param Context       $context
     * @param callable      $example
     * @param Specification $spec
     *
     * @return bool
     */
    private function runExample(Context $context, callable $example, Specification $spec)
    {
        $prophet = new Prophet();

        $this->prepareForTest($context, $spec, $prophet);

        $result = $this->functionRunner->run($example, $context, $prophet);

        $prophet->checkPredictions();

        return $result === null || $result === true;
    }

    /**
     * @param Context       $context
     * @param Specification $spec
     * @param Prophet       $prophet
     */
    private function prepareForTest(Context $context, Specification $spec, Prophet $prophet)
    {
        foreach ($context->getLetCallbacks() as $let) {
            $this->functionRunner->run($let, $context, $prophet);
        }

        $this->buildObjectInstance($context, $spec, $prophet);

        foreach ($context->getModifiers() as $modifier) {
            $this->functionRunner->run($modifier, $context, $prophet);
        }

        foreach ($context->getActions() as $name => $action) {
            $result = $this->functionRunner->run($action, $context, $prophet);

            if (is_string($name)) {
                $context->addCompiledValue($name, $result);
            }
        }
    }

    /**
     * @param Context       $context
     * @param Specification $spec
     * @param Prophet       $prophet
     */
    private function buildObjectInstance(Context $context, Specification $spec, Prophet $prophet)
    {
        $classReflection = new ReflectionClass($spec->getTitle());

        $parameters = [ ];
        foreach ($spec->getConstructorParameters() as $parameter) {
            $result       = $this->functionRunner->run($context->getValue($parameter), $context, $prophet);
            $parameters[] = $context->addCompiledValue($parameter, $result);
        }

        $context->addCompiledValue('that', $classReflection->newInstanceArgs($parameters));
    }
}