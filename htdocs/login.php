<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

$disable_verifyemail = true;
require __DIR__ . '/lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$tpl->name = 'login';
$tpl->menuitem = MNU_LOGIN;

$login->verify();

$tpl->assign('error', LOGIN_OK);

$target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'myhome.php';

// #1086 important change don't delete it :-)
$path = parse_url($target, PHP_URL_PATH);
if ((($path && !file_exists(__DIR__ . '/' . $path)) || !$path) && strpos($target, 'okapi/apps/') !== 0) {
    $target = 'myhome.php';
}

if (mb_strtolower(mb_substr($target, 0, 9)) === 'login.php') {
    $target = 'myhome.php';
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : ''; // Ocprop

if ($action == 'cookieverify') {
    // we should be logged in ... check if cookie is set ...
    if (!isset($_COOKIE[$opt['session']['cookiename'] . 'data'])) {
        $tpl->error(ERROR_NO_COOKIES);
    } else {
        $tpl->redirect($target);
    }
} elseif ($action == 'logout') {
    $login->logout();
    $tpl->assign('error', LOGIN_LOGOUT_OK);

    // Now a login prompt will be shown, and if a target has been supplied,
    // we will be redirected there after login. Note that OKAPI's
    // oauth/authorize interactivity=confirm_user function relies on this
    // "relogin" behavior!
} else {
    if ($login->userid != 0) {
        $tpl->error(ERROR_ALREADY_LOGGEDIN);
    }

    $username = isset($_POST['email']) ? trim($_POST['email']) : ''; // Ocprop
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // Ocprop

    $retval = $login->try_login($username, $password, null);
    $password = '';
    if ($retval == LOGIN_OK) {
        $tpl->redirect('login.php?action=cookieverify&target=' . urlencode($target));
    }

    $tpl->assign('username', $username);
    if (isset($_POST['password'])) {
        $tpl->assign('error', $retval);
    } else {
        $tpl->assign('error', LOGIN_OK);
    }
}
$tpl->assign('loginhelplink', helppagelink('login'));
$tpl->assign('target', $target);

$tpl->display();
