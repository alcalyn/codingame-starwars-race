<?php

class TrajectoryPoint extends Entity
{
    /**
     * @param Entity $entity
     *
     * @return self
     */
    public static function from(Entity $entity)
    {
        return new self($entity->x, $entity->y);
    }
}
