<?php

$map = new Map();
$ai = new AI($map);

while (true) {
    $map->updateFromStream(STDIN);

    echo $ai->nextMove().PHP_EOL;
}
