<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Cookie handling
 *  See doc/cookies.txt for more information in cookies.
 ***************************************************************************/

global $opt;

if ($opt['session']['mode'] == SAVE_SESSION) {
    // Do not use, not completely implemented yet
    $cookie = new Oc\Session\SessionDataNative();
} else {
    $cookie = new Oc\Session\SessionDataCookie();
}
