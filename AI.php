<?php

class AI
{
    /**
     * @var Map
     */
    public $map;

    /**
     * @var Trajectory
     */
    public $trajectory;

    /**
     * @var Entity
     */
    public $lastNextCheckpoint;

    /**
     * @param Map $map
     */
    public function __construct(Map $map)
    {
        $this->map = $map;
        $this->trajectory = null;
        $this->lastNextCheckpoint = null;
    }

    /**
     * @return Move
     */
    public function nextMove()
    {
        $myPod = $this->map->myPods[0];

        if ($this->map->looped) {
            $newCheckpoint = null === $this->lastNextCheckpoint || $this->lastNextCheckpoint->isNotAt($this->map->getNextCheckpoint());
            $currentPoint = $this->trajectory->getCurrentPoint();
            $lostTrajectory = $myPod->distanceTo($this->trajectory->coords[$currentPoint]) > 600;

            if ($newCheckpoint || $lostTrajectory) {
                error_log('Process new trajectory');
                $this->trajectory = null;
                $this->lastNextCheckpoint = $this->map->getNextCheckpoint();
            }

            $target = $this->followTrajectory($myPod);
        } else {
            $target = $this->map->getNextCheckpoint();
            $target = $this->calculateCounterSteering($myPod, $target);
        }

        //$target = $this->calculateCounterSteering($myPod, $target);

        return new Move($target, 100);
    }

    /**
     * @param Pod $myPod
     *
     * @return Entity recommended target.
     */
    public function followTrajectory(Pod $myPod)
    {
        if (null === $this->trajectory) {
            $nextCheckpoints = [
                $this->map->getNextCheckpoint(),
                $this->map->getNextCheckpoint(2),
                $this->map->getNextCheckpoint(3),
            ];

            $this->trajectory = new Trajectory($myPod, $nextCheckpoints);
        }

        $this->trajectory->updatePod($myPod);
        $currentPoint = $this->trajectory->getCurrentPoint();

        error_log('Trajectory current point: '.$currentPoint);
        error_log('Trajectory gap: '.$myPod->distanceTo($this->trajectory->coords[$currentPoint]));

        return $this->trajectory->coords[min($currentPoint + 5, count($this->trajectory->coords) - 1)];
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
