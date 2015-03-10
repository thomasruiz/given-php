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
            if (!is_object($this->instances[$name]) || $this->instances[$name] instanceof Closure) {
                $this->instances[$name] = $this->instances[$name]();
            }

            return $this->instances[ $name ];
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