<?php

class Trajectory
{
    /**
     * Space between each trajectory point.
     *
     * @var int
     */
    public static $GAP_INTERVAL = 200;

    /**
     * @var Pod
     */
    public $pod;

    /**
     * @var int
     */
    public $lastCoordsKey;

    /**
     * @var Checkpoint[]
     */
    public $nextCheckpoints;

    /**
     * @var TrajectoryPoint[]
     */
    public $coords;

    /**
     * @param Pod $pod
     * @param Checkpoint[] $nextCheckpoints
     */
    public function __construct(Pod $pod, array $nextCheckpoints)
    {
        $this->pod = $pod;
        $this->nextCheckpoints = $nextCheckpoints;
        $this->lastCoordsKey = 0;

        $this->process();
    }

    public function process()
    {
        $this->coords = [];

        if (count($this->nextCheckpoints) < 2) {
            throw new RuntimeException('Trajectory requiers at least 2 checkpoints.');
        }

        $controlPoints = [];
        $checkpoints = [$this->pod];
        $angles = [];

        foreach ($this->nextCheckpoints as $checkpoint) {
            $checkpoints []= $checkpoint;
        }

        for ($i = 0; $i < count($checkpoints) - 2; $i++) {
            $angles[$i] = $checkpoints[$i + 1]->rotationBetween($checkpoints[$i], $checkpoints[$i + 2]);
        }

        for ($i = 0; $i < count($checkpoints) - 1; $i++) {
            if (0 === $i) {
                $controlPoints []= [
                    $this->pod->add($this->pod->vector->mul(5)),
                    $checkpoints[$i    ]->rotate(-self::sign($angles[$i    ]) * (M_PI - abs($angles[$i    ])) / 2, $checkpoints[$i + 1])->homothety(1 / 3, $checkpoints[$i + 1]),
                ];
            } elseif ((count($checkpoints) - 2) === $i) {
                $controlPoints []= [
                    $checkpoints[$i + 1]->rotate(+self::sign($angles[$i - 1]) * (M_PI - abs($angles[$i - 1])) / 2, $checkpoints[$i    ])->homothety(1 / 3, $checkpoints[$i    ]),
                ];
            } else {
                $controlPoints []= [
                    $checkpoints[$i + 1]->rotate(+self::sign($angles[$i - 1]) * (M_PI - abs($angles[$i - 1])) / 2, $checkpoints[$i    ])->homothety(1 / 3, $checkpoints[$i    ]),
                    $checkpoints[$i    ]->rotate(-self::sign($angles[$i    ]) * (M_PI - abs($angles[$i    ])) / 2, $checkpoints[$i + 1])->homothety(1 / 3, $checkpoints[$i + 1]),
                ];
            }
        }

        for ($i = 0; $i < count($checkpoints) - 1; $i++) {
            $checkpoint0 = $checkpoints[$i];
            $checkpoint1 = $checkpoints[$i + 1];
            $distance = $checkpoint0->distanceTo($checkpoint1);
            $gapCount = intval(round($distance / self::$GAP_INTERVAL));

            if (0 === $gapCount) {
                $this->coords []= TrajectoryPoint::from($checkpoint1);
                continue;
            }

            $gap = 1 / $gapCount;
            $points = [$checkpoint0];

            foreach ($controlPoints[$i] as $controlPoint) {
                $points []= $controlPoint;
            }

            $points []= $checkpoint1;

            for ($j = 1; $j <= $gapCount; $j++) {
                $this->coords []= TrajectoryPoint::from(self::bezier($points, $j * $gap));
            }
        }

        d($angles);
        d($checkpoints);
        d($controlPoints);
    }

    /**
     * @param Pod $pod
     */
    public function updatePod(Pod $pod)
    {
        $this->pod = $pod;
    }

    /**
     * @param Entity $entity
     *
     * @return int Key of nearest coords.
     */
    public function getCurrentPoint()
    {
        $nearestCoordsDistance = $this->pod->distanceTo($this->coords[$this->lastCoordsKey]);

        while (($distance = $this->pod->distanceTo($this->coords[$this->lastCoordsKey + 1])) <= $nearestCoordsDistance) {
            $this->lastCoordsKey++;
            $nearestCoordsDistance = $distance;
        }

        return $this->lastCoordsKey;
    }

    /**
     * @param Coords[] $points
     * @param float $t
     *
     * @return Coords
     */
    public static function bezier(array $points, $t)
    {
        if (count($points) < 3) {
            return $points[0]->add($points[1]->sub($points[0])->mul($t));
        }

        $newPoints = [];

        for ($i = 1; $i < count($points); $i++) {
            $newPoints []= self::bezier([$points[$i - 1], $points[$i]], $t);
        }

        return self::bezier($newPoints, $t);
    }

    /**
     * @param float $n
     *
     * @return int
     */
    private static function sign($n)
    {
        return $n < 0 ? -1 :  1;
    }
}
