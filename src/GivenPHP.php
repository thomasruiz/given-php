<?php namespace GivenPHP;

use GivenPHP\TestSuite\Context;
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
     * @var GivenPHP
     */
    private static $instance;

    /**
     * @var Suite
     */
    private $suite;

    /**
     * @var Specification
     */
    private $currentSpec;

    /**
     * Construct a new GivenPHP object.
     *
     * @param Suite $suite
     */
    public function __construct(Suite $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self(new Suite());
        }

        return self::$instance;
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
        $spec = new Specification($classUnderSpec, $constructorArguments, new Context($classUnderSpec, $callback));
        $this->suite->addSpecification($spec);
        $this->currentSpec = $spec;

        return $spec;
    }

    /**
     * Add a context to the spec.
     *
     * @param string   $context
     * @param callable $callback
     */
    public function addContext($context, callable $callback)
    {
        $parentContext = $this->currentSpec->getCurrentContext();
        $context       = new Context($context, $callback, $parentContext);
        $this->currentSpec->addContext($context);
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
        return call_user_func_array([ self::getInstance(), $name ], $arguments);
    }

    /**
     * @return Suite
     */
    public function getSuite()
    {
        return $this->suite;
    }
}