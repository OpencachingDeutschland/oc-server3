<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Oc\Country\Country;

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'register';
$tpl->menuitem = MNU_START_REGISTER;

// Read register information
$show_all_countries = isset($_POST['show_all_countries']) ? $_POST['show_all_countries'] + 0 : 0;
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$password = isset($_POST['password1']) ? $_POST['password1'] : '';
$password2 = isset($_POST['password2']) ? $_POST['password2'] : '';
$email = isset($_POST['email']) ? mb_trim($_POST['email']) : '';
$sel_country = isset($_POST['country']) ? $_POST['country'] : 'XX';
$tos = isset($_POST['TOS']) ? ($_POST['TOS'] == 'ON') : false;

if (isset($_POST['show_all_countries_submit'])) {
    $show_all_countries = 1;
} elseif (isset($_POST['submit'])) {
    $bError = false;

    $user = new user(ID_NEW);
    if (!$user->setEMail($email)) {
        $bError = true;
        $tpl->assign('error_email_not_ok', 1);
    }

    if (!$user->setUsername($username)) {
        $bError = true;
        $tpl->assign('error_username_not_ok', 1);
    }

    if (!$user->setFirstName($first_name)) {
        $bError = true;
        $tpl->assign('error_first_name_not_ok', 1);
    }
    if (!$user->setLastName($last_name)) {
        $bError = true;
        $tpl->assign('error_last_name_not_ok', 1);
    }

    if (!$user->setPassword($password)) {
        $bError = true;
        $tpl->assign('error_password_not_ok', 1);
    } elseif ($password != $password2) {
        $bError = true;
        $tpl->assign('error_password_diffs', 1);
    }

    if (!$user->setCountryCode(($sel_country == 'XX') ? null : $sel_country)) {
        $bError = true;
        $tpl->assign('error_unkown', 1);
    }

    if ($tos !== true) {
        $bError = true;
        $tpl->assign('error_tos_not_ok', 1);
    }

    if ($bError === false) {
        // try to register
        $user->setActivationCode($user->CreateCode());
        $user->setNode($opt['logic']['node']['id']);

        if ($user->save()) {
            // send confirmation
            $user->sendRegistrationCode();

            //display confirmation
            $tpl->assign('confirm', 1);
        } else {
            $bReasonFound = false;

            // username or email already exists
            if ($user->existUsername($user->getUsername())) {
                $tpl->assign('error_username_exists', 1);
                $bReasonFound = true;
            }

            if ($user->existEMail($user->getEMail())) {
                $tpl->assign('error_email_exists', 1);
                $bReasonFound = true;
            }

            if ($bReasonFound === false) {
                $tpl->assign('error_unkown', 1);
            }
        }
    }
}

$country = new Country($sel_country == 'XX' ? $opt['page']['main_country'] : $sel_country, 'profile');
if (!$country->isMain()) {
    $show_all_countries = 1;
}
if ($show_all_countries == 1) {
    $rs = $country->getAllRS();
} else {
    $rs = $country->getMainRS();
}
$tpl->assign_rs('countries', $rs);
sql_free_result($rs);

$tpl->assign('show_all_countries', $show_all_countries);
$tpl->assign('country', $sel_country);
$tpl->assign('country_full', $country->getLocaleName());

$tpl->assign('email', $email);
$tpl->assign('first_name', $first_name);
$tpl->assign('last_name', $last_name);
$tpl->assign('username', $username);

$tpl->display();
