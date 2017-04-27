<?php

class AI
{
    /**
     * @var Map
     */
    public $map;

    /**
     * @param Map $map
     */
    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    /**
     * @return Move
     */
    public function nextMove()
    {
        $myPod = $this->map->myPods[0];
        $target = $this->map->nextCheckpoint;

        $target = $this->calculateCounterSteering($myPod, $target);

        return new Move($target, 100);
    }

    /**
     * @param Pod $myPod
     * @param Entity $target
     *
     * @return Entity
     */
    public static function calculateCounterSteering(Pod $myPod, Entity $target)
    {
        if ($myPod->vector->isOrigin()) {
            return $target;
        }

        $rotationToTarget = $myPod->rotationBetween($target, $myPod->add($myPod->vector));

        if (abs($rotationToTarget) > M_PI_2) {
            return $target;
        }

        return $target->rotate(-$rotationToTarget / 2, $myPod);
    }
}
