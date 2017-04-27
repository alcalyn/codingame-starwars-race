<?php

class Move
{
    /**
     * @var Entity
     */
    public $target;

    /**
     * @var int
     */
    public $thrust;

    /**
     * @param Entity $target
     * @param int $thrust
     */
    public function __construct(Entity $target, $thrust = 100)
    {
        $this->target = $target;
        $this->thrust = $thrust;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $x = intval($this->target->x);
        $y = intval($this->target->y);
        $thrust = 0;

        if ($this->thrust < 0) {
            $thrust = 0;
        } elseif ($this->thrust > 100) {
            $thrust = 100;
        } else {
            $thrust = intval(round($this->thrust));
        }

        return "$x $y $thrust";
    }
}
