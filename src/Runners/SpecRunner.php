<?php namespace GivenPHP\Runners;

use GivenPHP\TestSuite\Context;
use GivenPHP\TestSuite\Specification;
use Prophecy\Prophet;
use ReflectionClass;

class SpecRunner
{

    /**
     * @var FunctionRunner
     */
    private $functionRunner;

    /**
     * Construct a new SpecRunner object
     *
     * @param FunctionRunner $exampleRunner
     */
    public function __construct(FunctionRunner $exampleRunner)
    {
        $this->functionRunner = $exampleRunner;
    }

    /**
     * @param Specification $spec
     */
    public function run(Specification $spec)
    {
        foreach ($spec->getContexts() as $context) {
            $this->runExamples($context, $spec);
        }
    }

    /**
     * @param Context       $context
     * @param Specification $spec
     */
    private function runExamples(Context $context, Specification $spec)
    {
        foreach ($context->getExamples() as $example) {
            $this->runExample(clone $context, $example, $spec);
        }
    }

    /**
     * @param Context       $context
     * @param callable      $example
     * @param Specification $spec
     */
    private function runExample(Context $context, callable $example, Specification $spec)
    {
        $prophet = new Prophet();

        foreach ($context->getLetCallbacks() as $let) {
            $this->functionRunner->run($let, $context, $prophet);
        }

        $classReflection = new ReflectionClass($spec->getTitle());

        $parameters = [ ];
        foreach ($spec->getConstructorParameters() as $parameter) {
            $result       = $this->functionRunner->run($context->getValue($parameter), $context, $prophet);
            $parameters[] = $context->addCompiledValue($parameter, $result);
        }

        $context->addCompiledValue('that', $classReflection->newInstanceArgs($parameters));

        foreach ($context->getActions() as $action) {
            $this->functionRunner->buildParameters($action, $context, $prophet);
        }

        foreach ($context->getModifiers() as $modifier) {
            $this->functionRunner->run($modifier, $context, $prophet);
        }

        foreach ($context->getActions() as $name => $action) {
            $result = $this->functionRunner->run($action, $context, $prophet);

            if (is_string($name)) {
                $context->addCompiledValue($name, $result);
            }
        }

        $result = $this->functionRunner->run($example, $context, $prophet);
        var_dump($result);

        $prophet->checkPredictions();
    }
}