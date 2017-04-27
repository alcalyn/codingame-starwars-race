<?php

class Trajectory
{
    /**
     * @var Pod
     */
    public $pod;

    /**
     * @var Checkpoint[]
     */
    public $nextCheckpoints;

    /**
     * @var Coords[]
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
                    $this->pod->add($this->pod->vector),
                    $checkpoints[$i    ]->rotate(+(180 - $angles[$i    ]) / 2, $checkpoints[$i + 1])->div(3),
                ];
            } elseif ((count($checkpoints) - 2) === $i) {
                $controlPoints []= [
                    $checkpoints[$i + 1]->rotate(-(180 - $angles[$i - 1]) / 2, $checkpoints[$i    ])->div(3),
                ];
            } else {
                $controlPoints []= [
                    $checkpoints[$i + 1]->rotate(-(180 - $angles[$i - 1]) / 2, $checkpoints[$i    ])->div(3),
                    $checkpoints[$i    ]->rotate(+(180 - $angles[$i    ]) / 2, $checkpoints[$i + 1])->div(3),
                ];
            }
        }

        $controlPoints[0][0] = $this->pod->add($this->pod->vector);

        echo $this->pod.PHP_EOL;

        foreach ($this->nextCheckpoints as $c) {
            echo $c.PHP_EOL;
        }

        foreach ($controlPoints as $cs) {
            echo "Control points:\n";

            foreach ($cs as $c) {
                echo "    $c\n";
            }
        }
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
}
