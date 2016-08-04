<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Cookie handling
 *  See doc/cookies.txt for more information in cookies.
 ***************************************************************************/

require_once 'SessionDataCookie.class.php';
require_once 'SessionDataNative.class.php';

global $opt;

if ($opt['session']['mode'] == SAVE_SESSION) {
    // Do not use, not completely implemented yet
    $cookie = new SessionDataNative();
} else {
    $cookie = new SessionDataCookie();
}
