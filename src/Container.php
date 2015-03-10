<?php namespace GivenPHP;

use Closure;
use ReflectionClass;

class Container
{

    /**
     * @var object[]
     */
    private $instances = [ ];

    /**
     * @var string[]
     */
    private $definitions = [ ];

    /**
     * @param string          $name
     * @param callable|object $instance
     *
     * @return mixed
     */
    public function shared($name, $instance = null)
    {
        if ($instance === null) {
            return $this->instances[ $name ];
        }

        if (!is_object($instance) || $instance instanceof Closure) {
            $instance = $instance();
        }

        $this->instances[ $name ] = $instance;

        return $this;
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return $this
     */
    public function define($name, $class)
    {
        $this->definitions[ $name ] = $class;

        return $this;
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public function build($name, $args = [ ])
    {
        $class = new ReflectionClass($this->definitions[ $name ]);

        return $class->newInstanceArgs($args);
    }
}