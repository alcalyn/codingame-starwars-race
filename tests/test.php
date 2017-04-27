<?php

require_once '../functions.php';
require_once '../Entity.php';

foreach (glob('../[A-Z]*.php') as $filename) {
    echo $filename.PHP_EOL;
    require_once $filename;
}

function assertEquals($expected, $actual) {
    if ($expected === $actual) {
        display('OK');
    } else {
        display("ERROR, expected $expected, got $actual");
    }
}

function display($msg) {
    echo $msg.PHP_EOL;
}



display('ROTATE COORDS');

$a = new Coords(10, 10);
$b = new Coords(20, 10);

$c = $b->rotate(M_PI / 2, $a);

assertEquals(10.0, $c->x);
assertEquals(20.0, $c->y);

$d = $b->rotate(0, $a);

assertEquals(20.0, $d->x);
assertEquals(10.0, $d->y);



display('ROTATION BETWEEN');

$a = new Coords(0, 20);
$b = new Coords(20, 20);
$o = new Coords(10, 10);

assertEquals(-M_PI_2, $o->rotationBetween($a, $b));


display('COUNTER STEERING');

display('no initial speed');
$checkpoint = new Checkpoint(10, 10);
$pod = new Pod(0, 0);
$pod->vector = new Coords(0, 0);

$newTarget = AI::calculateCounterSteering($pod, $checkpoint);

assertEquals(10, $newTarget->x);
assertEquals(10, $newTarget->y);

display('target in front, no counter steering expected');
$checkpoint = new Checkpoint(10, 10);
$pod = new Pod(0, 0);
$pod->vector = new Coords(10, 10);

$newTarget = AI::calculateCounterSteering($pod, $checkpoint);

assertEquals(10.0, $newTarget->x);
assertEquals(10.0, $newTarget->y);

display('target on my left');
$checkpoint = new Checkpoint(10, 10);
$pod = new Pod(0, 0);
$pod->vector = new Coords(10, 0);

$newTarget = AI::calculateCounterSteering($pod, $checkpoint);

assertEquals(5, intval($newTarget->x));
assertEquals(13, intval($newTarget->y));

display('target on my right');
$checkpoint = new Checkpoint(10, 10);
$pod = new Pod(0, 0);
$pod->vector = new Coords(0, 10);

$newTarget = AI::calculateCounterSteering($pod, $checkpoint);

assertEquals(13, intval($newTarget->x));
assertEquals(5, intval($newTarget->y));



display('BEZIER');

$coords = [
    new Coords(10, 10),
    new Coords(30, 100),
    new Coords(90, -80),
    new Coords(100, 20),
];

$time = microtime(true);

for ($i = 0; $i <= 1; $i += 0.1) {
    d(Trajectory::bezier($coords, $i));
}

d(microtime(true) - $time);



display('TRAJECTORY');

$checkpoints = [
    new Coords(100, 10),
    new Coords(40, 100),
    new Coords(0, 100),
];
$pod = new Pod(10, 10);
$pod->vector = new Coords(30, -20);

$time = microtime(true);

$trajectory = new Trajectory($pod, $checkpoints);

d(microtime(true) - $time);
