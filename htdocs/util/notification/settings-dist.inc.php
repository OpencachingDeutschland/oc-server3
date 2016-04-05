<?php
/***************************************************************************
 *    For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

if (!isset($maildomain)) {
    $maildomain = 'opencaching.de';
}

$mailfrom = 'noreply@' . $maildomain;

$debug = false;
$debug_mailto = 'abc@xyz.de';

$notifypid = $rootpath . 'cache/notify.pid';
