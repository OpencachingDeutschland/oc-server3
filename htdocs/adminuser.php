<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'adminuser';
$tpl->menuitem = MNU_ADMIN_USER;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

if (($login->admin & ADMIN_USER) != ADMIN_USER) {
    $tpl->error(ERROR_NO_ACCESS);
}

if (isset($_REQUEST['success']) && $_REQUEST['success']) {
    $tpl->assign('success', '1');
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'display';

if ($action === 'searchuser') {
    searchUser();
} elseif ($action === 'sendcode') {
    sendCode();
} elseif ($action === 'formaction') {
    formAction();
} elseif ($action === 'display') {
    $tpl->display();
}

$tpl->error(ERROR_UNKNOWN);

function sendCode()
{
    global $tpl;

    $userId = isset($_REQUEST['userid']) ? $_REQUEST['userid'] + 0 : 0;

    $user = new user($userId);
    if ($user->exist() == false) {
        $tpl->error(ERROR_UNKNOWN);
    }

    // send a new confirmation
    $user->sendRegistrationCode();

    $tpl->redirect('adminuser.php?action=searchuser&msg=sendcodecommit&username=' . urlencode($user->getUsername()));
}

function formAction()
{
    global $tpl, $translate;

    $commit = isset($_REQUEST['chkcommit']) ? $_REQUEST['chkcommit'] + 0 : 0;
    $delete = isset($_REQUEST['chkdelete']) ? $_REQUEST['chkdelete'] + 0 : 0;
    $disable = isset($_REQUEST['chkdisable']) ? $_REQUEST['chkdisable'] + 0 : 0;
    $emailProblem = isset($_REQUEST['chkemail']) ? $_REQUEST['chkemail'] + 0 : 0;
    $dataLicense = isset($_REQUEST['chkdl']) ? true : false;
    $userId = isset($_REQUEST['userid']) ? $_REQUEST['userid'] + 0 : 0;
    $disduelicense = isset($_REQUEST['chkdisduelicense']) ? $_REQUEST['chkdisduelicense'] + 0 : 0;

    $user = new user($userId);
    if ($user->exist() == false) {
        $tpl->error(ERROR_UNKNOWN);
    }
    $username = $user->getUsername();

    if ($delete + $disable + $disduelicense > 1) {
        $tpl->error($translate->t('Please select only one of the delete/disable options!', '', '', 0));
    }

    if ($commit == 0) {
        $tpl->error($translate->t('You have to check that you are sure!', '', '', 0));
    }

    if ($disduelicense == 1) {
        $errorMessage = $user->disduelicense();
        if ($errorMessage !== true) {
            $tpl->error($errorMessage);
        }
    } elseif ($disable == 1) {
        if ($user->disable() == false) {
            $tpl->error(ERROR_UNKNOWN);
        }
    } elseif ($delete == 1) {
        if ($user->delete() == false) {
            $tpl->error(ERROR_UNKNOWN);
        }
    } elseif ($emailProblem == 1) {
        $user->addEmailProblem($dataLicense);
    }

    $tpl->redirect('adminuser.php?action=searchuser&username=' . urlencode($username) .
        '&success=' . ($disduelicense + $disable));
}

function searchUser()
{
    global $tpl, $opt;

    $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
    $msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';

    $tpl->assign('username', $username);
    $tpl->assign('msg', $msg);

    /** @var Connection $connection */
    $connection = AppKernel::Container()->get(Connection::class);
    $r = $connection->fetchAssoc(
        'SELECT `user_id`,
                `username`,
                `email`,
                `email_problems`,
                `date_created`,
                `last_modified`,
                `is_active_flag`,
                `activation_code`,
                `first_name`,
                `last_name`,
                `last_login`,
                `data_license`=:dataLicense AS `license_declined`
         FROM `user`
         WHERE `username`= :user
         OR `email`=:user',
        [
            'user' => $username,
            'dataLicense' => NEW_DATA_LICENSE_ACTIVELY_DECLINED
        ]
    );

    if (!$r) {
        $tpl->assign('error', 'userunknown');
        $tpl->display();
    }

    $tpl->assign('showdetails', true);

    $r['hidden'] = (int) $connection->fetchColumn(
        'SELECT COUNT(*) FROM `caches` WHERE `user_id`=:userId', [':userId' => $r['user_id']]
    );
    $r['hidden_active'] = (int) $connection->fetchColumn(
        'SELECT COUNT(*) FROM `caches` WHERE `user_id`= :userId AND `status`=1',
        [':userId' => $r['user_id']]
    );
    $r['logentries'] = (int) $connection->fetchColumn(
        'SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`= :userId',
        [':userId' => $r['user_id']]
    );
    $r['deleted_logentries'] = (int) $connection->fetchColumn(
        'SELECT COUNT(*) FROM `cache_logs_archived` WHERE `user_id`= :userId',
        [':userId' => $r['user_id']]
    );
    $r['reports'] = (int) $connection->fetchColumn(
        'SELECT COUNT(*) FROM `cache_reports` WHERE `userid`= :userId',
        [':userId' => $r['user_id']]
    );

    $tpl->assign('user', $r);

    $user = new user($r['user_id']);
    if (!$user->exist()) {
        $tpl->error(ERROR_UNKNOWN);
    }
    $tpl->assign('candisable', $user->canDisable());
    $tpl->assign('candelete', $user->canDelete());
    $tpl->assign('cansetemail', !$user->missedDataLicenseMail() && $r['email'] != "");
    $tpl->assign('licensefunctions', $opt['logic']['license']['admin']);

    $tpl->display();
}
