<?php

class Pod extends Entity
{
    public $rotation;

    /**
     * @var Entity
     */
    public $vector;

    /**
     * @param int $x
     * @param int $y
     */
    public function __construct($x = 0, $y = 0)
    {
        parent::__construct($x, $y);

        $this->vector = new Coords(0, 0);
    }
}
