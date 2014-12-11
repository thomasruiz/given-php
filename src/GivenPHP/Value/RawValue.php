<?php namespace GivenPHP\Value;

class RawValue
{

    /**
     * The label explaining the value.
     *
     * @var string
     */
    private $label;

    /**
     * The name of the value.
     *
     * @var string
     */
    private $name;

    /**
     * The raw value stored.
     *
     * @var mixed
     */
    private $value;

    /**
     * Construct a new RawValue object.
     *
     * @param string $label
     * @param string $name
     * @param mixed  $value
     */
    public function __construct($label, $name, $value)
    {
        $this->label = $label;
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * Get the raw value of the instance.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the name of the raw value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
