<?php


//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

require __DIR__ . '/vendor/autoload.php';

use OcLegacy\Map\StaticMap;

$map = new StaticMap();
echo $map->showMap();
