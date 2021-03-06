<?php namespace GivenPHP;

use GivenPHP\TestSuite\Specification;
use GivenPHP\TestSuite\Suite;

/**
 * Class GivenPHP
 * @method static void addValue( $name, callable $callback )
 * @method static void addModifier( callable $callback )
 * @method static void addActionWithResult( $name, callable $callback )
 * @method static void addActionWithoutResult( callable $callback )
 * @method static void addLetCallback( callable $callback )
 * @method static void addExample( callable $callback )
 */
class GivenPHP
{

    /**
     * @var Suite
     */
    private $suite;

    /**
     * @var Specification
     */
    private $currentSpec;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var self
     */
    private static $instance;

    /**
     * Construct a new GivenPHP object.
     *
     * @param Container $container
     * @param Suite     $suite
     */
    public function __construct(Container $container, Suite $suite)
    {
        $this->suite     = $suite;
        $this->container = $container;
        self::$instance  = $this;
    }

    /**
     * Describe keyword.
     *
     * @param string   $classUnderSpec
     * @param array    $constructorArguments
     * @param callable $callback
     *
     * @return Specification
     */
    public function describe($classUnderSpec, array $constructorArguments, callable $callback)
    {
        $context = $this->container->build('testsuite.context', [ $classUnderSpec, $callback ]);
        $spec    = $this->container->build('testsuite.spec', [ $classUnderSpec, $constructorArguments, $context ]);

        $this->suite->addSpecification($spec);

        return $this->currentSpec = $spec;
    }

    /**
     * Add a context to the spec.
     *
     * @param string   $context
     * @param callable $callback
     */
    public function addContext($context, callable $callback)
    {
        $params = [ $context, $callback, $this->currentSpec->getCurrentContext() ];

        $this->currentSpec->addContext($this->container->build('testsuite.context', $params));
    }

    /**
     * Proxy to the current spec.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([ $this->currentSpec, $name ], $arguments);
    }

    /**
     * Proxy to the instance.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([ self::$instance, $name ], $arguments);
    }

    /**
     * @return Suite
     */
    public function getSuite()
    {
        return $this->suite;
    }
}