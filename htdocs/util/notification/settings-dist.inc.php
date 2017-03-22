<?php
/***************************************************************************
 *    For license information see doc/license.txt
 ***************************************************************************/

if (!isset($maildomain)) {
    $maildomain = 'opencaching.de';
}

$mailfrom = 'noreply@' . $maildomain;

$debug = false;
$debug_mailto = 'abc@xyz.de';

$notifypid = $rootpath . 'var/cache2/notify.pid';
