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
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor
     *
     * @param $type
     * @param $label
     * @param $associatedName
     */
    public function __construct($type, $label, $associatedName = null)
    {
        $this->type  = $type;
        $this->label = $label;
        $this->name  = $associatedName;
    }

    /**
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
