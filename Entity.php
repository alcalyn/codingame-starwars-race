<?php

class Entity
{
    /**
     * @var int
     */
    public $x;

    /**
     * @var int
     */
    public $y;

    /**
     * @param int $x
     * @param int $y
     */
    public function __construct($x = 0, $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @param Entity $entity
     *
     * @return bool
     */
    public function isAt(Entity $entity)
    {
        return
            $this->x === $entity->x &&
            $this->y === $entity->y
        ;
    }

    /**
     * @param Entity $entity
     *
     * @return Coords
     */
    public function add(Entity $entity)
    {
        return new Coords($this->x + $entity->x, $this->y + $entity->y);
    }

    /**
     * @param Entity $entity
     *
     * @return Coords
     */
    public function sub(Entity $entity)
    {
        return new Coords($this->x - $entity->x, $this->y - $entity->y);
    }

    /**
     * @param float $n
     *
     * @return Coords
     */
    public function mul($n)
    {
        return new Coords($this->x * $n, $this->y * $n);
    }

    /**
     * @param float $n
     *
     * @return Coords
     */
    public function div($n)
    {
        return new Coords($this->x / $n, $this->y / $n);
    }

    /**
     * @return bool
     */
    public function isOrigin()
    {
        return 0 === $this->x && 0 === $this->y;
    }

    /**
     * @param Entity $entity
     *
     * @return float
     */
    public function distanceTo(Entity $entity)
    {
        $diff = $entity->sub($this);

        return sqrt($diff->x * $diff->x + $diff->y * $diff->y);
    }

    /**
     * @param float $angle in radians
     * @param Entity $origin (by default origin)
     *
     * @return Coords
     */
    public function rotate($angle, Entity $origin = null)
    {
        if (null === $origin) {
            $origin = new Coords(0, 0);
        }

        $diff = $this->sub($origin);

        return new Coords(
            $origin->x + $diff->x * cos($angle) - $diff->y * sin($angle),
            $origin->y + $diff->x * sin($angle) + $diff->y * cos($angle)
        );
    }

    /**
     * @param Entity $entity0
     * @param Entity $entity1
     *
     * @return float
     */
    public function rotationBetween(Entity $entity0, Entity $entity1)
    {
        $vector0 = $entity0->sub($this);
        $vector1 = $entity1->sub($this);

        return atan2($vector1->y, $vector1->x) - atan2($vector0->y, $vector0->x);
    }

    /**
     * @param Entity $target
     * @param Entity $reference
     *
     * @return bool
     */
    public function isLeft(Entity $target, Entity $reference)
    {
        $diff = $target->sub($reference);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this)." $this->x $this->y";
    }
}
