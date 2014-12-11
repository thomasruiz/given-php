<?php namespace GivenPHP\Value;

class RawValue
{

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $name;

    /**
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
