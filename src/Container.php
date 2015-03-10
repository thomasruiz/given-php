<?php namespace GivenPHP;

use Closure;

class Container
{

    /**
     * @var object[]
     */
    private $instances;

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

        $this->instances[$name] = $instance;

        return $this;
    }
}