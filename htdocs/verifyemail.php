<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$disable_verifyemail = true;
require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'verifyemail';
$tpl->menuitem = MNU_VERIFY_EMAIL;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

$orgpage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'index.php';
$user = new user($login->userid);

if (isset($_REQUEST['new'])) {
    $tpl->redirect('newemail.php');
} else {
    if (isset($_REQUEST['confirm'])) {
        $user->shownDataLicenseMail();
        $user->confirmEmailAddress();
        $tpl->redirect($orgpage);
    } else {
        $tpl->assign('emailadr', $user->getEMail());
        if ($user->missedDataLicenseMail()) {
            $tpl->assign('datalicensemail', "<br /><br />" . file_get_contents("resource2/misc/datalicensemail.html"));
        }
        $tpl->assign('orgpage', $orgpage);
    }
}

$tpl->display();
