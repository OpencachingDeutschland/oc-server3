<?php
if (!isset($maildomain)) {
    $maildomain = 'opencaching.de';
}

$mailfrom = 'noreply@' . $maildomain;

$debug = false;
$debug_mailto = 'abc@xyz.de';

$watchpid = $rootpath . 'cache2/watch.pid';
