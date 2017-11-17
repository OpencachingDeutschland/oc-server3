<?php

date_default_timezone_set('Europe/Berlin');

require_once __DIR__ . '/../htdocs/vendor/autoload.php';

$env = 'prod';
$debug = true;

$kernel = new AppKernel($env, $debug);
$kernel->boot();

