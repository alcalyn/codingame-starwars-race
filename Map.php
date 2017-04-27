<?php

class Map
{
    /**
     * @var Checkpoint[]
     */
    public $checkpoints;

    /**
     * @var Pod[]
     */
    public $myPods;

    /**
     * @var Pod[]
     */
    public $opponentPods;

    /**
     * @var Checkpoint
     */
    public $nextCheckpoint;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->checkpoints = [];
        $this->myPods = [];
        $this->opponentPods = [];
    }

    /**
     * @param resource $stream
     */
    public function updateFromStream($stream)
    {
        fscanf(STDIN, "%d %d %d %d %d %d",
            $x,
            $y,
            $nextCheckpointX, // x position of the next check point
            $nextCheckpointY, // y position of the next check point
            $nextCheckpointDist, // distance to the next checkpoint
            $nextCheckpointAngle // angle between your pod orientation and the direction of the next checkpoint
        );
        fscanf(STDIN, "%d %d",
            $opponentX,
            $opponentY
        );

        d($nextCheckpointAngle);

        $myLastPod = null;

        if (count($this->myPods) > 0) {
            $myLastPod = $this->myPods[0];
        }

        $this->myPods = [new Pod($x, $y)];
        $this->opponentPods = [new Pod($opponentX, $opponentY)];
        $this->nextCheckpoint = new Checkpoint($nextCheckpointX, $nextCheckpointY);

        if (null !== $myLastPod) {
            $this->myPods[0]->vector = $this->myPods[0]->sub($myLastPod);
        }

        $nextNextCheckpoint = null;

        if (!$this->looped) {
            $this->memorizeCheckpoints($this->nextCheckpoint);
        } else {
            $nextNextCheckpoint = $this->getNextCheckpoint(2);
        }
    }

    /**
     * @param int $next
     *
     * @return Checkpoint
     */
    public function getNextCheckpoint($next = 1)
    {
        if (1 === $next) {
            return $this->nextCheckpoint;
        }

        foreach ($this->checkpoints as $key => $checkpoint) {
            if ($checkpoint->isAt($this->nextCheckpoint)) {
                return $this->checkpoints[($key + $next) % count($this->checkpoints)];
            }
        }

        throw new RuntimeException('Could not determine next checkpoint.');
    }

    private $looped = false;
    private function memorizeCheckpoints(Checkpoint $nextCheckpoint)
    {
        if (0 === count($this->checkpoints)) {
            $this->checkpoints []= $nextCheckpoint;
            return;
        }

        if ($this->checkpoints[count($this->checkpoints) - 1] === $nextCheckpoint) {
            return;
        }

        $this->checkpoints []= $nextCheckpoint;

        if ($this->checkpoints[0] === $nextCheckpoint) {
            $this->looped = true;
        }
    }
}
