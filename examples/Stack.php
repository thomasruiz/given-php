<?php

class Stack
{

    protected $items;

    public function __construct($items = [ ])
    {
        $this->items = $items;
    }

    public function push($item)
    {
        array_push($this->items, $item);
    }

    public function pop($item)
    {
        array_pop($this->items, $item);
    }

    public function size()
    {
        return count($this->items);
    }

    public function is_empty()
    {
        return count($this->items) === 0;
    }

    public function __tostring()
    {
        return 'Stack';
    }
}

