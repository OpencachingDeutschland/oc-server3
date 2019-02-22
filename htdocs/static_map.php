<?php

require __DIR__ . '/vendor/autoload.php';

use OcLegacy\Map\StaticMap;

$map = new StaticMap();
echo $map->showMap();
