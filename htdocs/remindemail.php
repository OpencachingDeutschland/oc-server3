<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'remindemail';
$tpl->menuitem = MNU_LOGIN_REMINDEMAIL;

$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';

if (isset($_REQUEST['ok'])) {
    if ($username == '') {
        $tpl->assign('errorUsernameInvalid', true);
    } else {
        $user = user::fromUsername($username);

        if ($user !== null) {
            if ($user->remindEMail()) {
                $tpl->assign('remindMailSent', true);
            } else {
                $tpl->assign('errorUnkown', true);
            }
        } else {
            $tpl->assign('errorUsernameNotExist', true);
        }
    }
}

$tpl->assign('username', $username);
$tpl->display();
