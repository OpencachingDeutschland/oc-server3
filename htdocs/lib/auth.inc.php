<?php
/***************************************************************************
 * ./lib/auth.inc.php
 * --------------------
 * begin                : Fri September 16 2005
 *
 * For license information see doc/license.txt
 ***************************************************************************/

/****************************************************************************
 *
 * Unicode Reminder メモ
 *
 * all login/logout related functions (reduced to auth_user, becuase
 * all other functions are handled by lib2/login.class.php)
 * Dont include this file by hand - it will be included from common.inc.php
 ****************************************************************************/

require $opt['rootpath'] . 'lib/login.class.php';

$autherr = 0;
define('AUTHERR_NOERROR', 0);
define('AUTHERR_TOOMUCHLOGINS', 1);
define('AUTHERR_INVALIDEMAIL', 2);
define('AUTHERR_WRONGAUTHINFO', 3);
define('AUTHERR_USERNOTACTIVE', 4);

/* auth_user - fills usr[]
 * no return value
 */
function auth_user()
{
    global $usr, $login;
    $login->verify();

    if ($login->userid != 0) {
        //set up $usr array
        $usr['userid'] = $login->userid;
        $usr['email'] = sqlValue("SELECT `email` FROM `user` WHERE `user_id`='" . sql_escape($login->userid) . "'", '');
        $usr['username'] = $login->username;
        $usr['admin'] = $login->admin;
    } else {
        $usr = false;
    }

    return;
}
