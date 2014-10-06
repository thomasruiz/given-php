<?php

namespace GivenPHP;

class Label
{

    /**
     * Type Given
     */
    const GIVEN = 'Given ';

    /**
     * Type When
     */
    const WHEN = 'When ';

    /**
     * The label explaining the associated name
     *
     * @var string $label
     */
    private $label;

    /**
     * The type of the label
     *
     * @var string
     */
    private $type;

    /**
     * The name of the value labelled
     *
     * @var string
     */
    private $name;

    /**
     * Constructor
     *
     * @param string $type
     * @param string $label
     * @param string $associatedName
     */
    public function __construct($type, $label, $associatedName = null)
    {
        $this->type  = $type;
        $this->label = $label;
        $this->name  = $associatedName;
    }

    /**
     * Prints the label
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->name !== null) {
            return $this->type . $this->label . ' (' . $this->name . ')';
        }

        return $this->type . $this->label;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->label === null;
    }
}
