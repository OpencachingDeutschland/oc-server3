<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

$disable_verifyemail = true;
require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'activation';
$tpl->menuitem = MNU_START_REGISTER_ACTIVATION;

// We use short param codes 'u' and 'c' to generate short-enough activation
// url that will not be wrapped in plain-text emails.

$code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : (isset($_REQUEST['c']) ? trim($_REQUEST['c']) : '');
$email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : (isset($_REQUEST['e']) ? trim($_REQUEST['e']) : '');

$tpl->assign('errorEMail', false);
$tpl->assign('errorCode', false);
$tpl->assign('errorAlreadyActivated', false);
$tpl->assign('sucess', false);

if (isset($_REQUEST['submit']) || ($code !== '' && $email !== '')) {
    $emailNotOk = is_valid_email_address($email) ? false : true;

    if ($emailNotOk === false) {
        /** @var Connection $connection */
        $connection = AppKernel::Container()->get(Connection::class);
        $activation = $connection
            ->fetchAssoc(
                'SELECT `user_id` `id`, `activation_code` `code` FROM `user` WHERE `email`=:email',
                [':email' => $email]
            );

        if ($activation) {
            if ($activation['code'] === $code) {
                $connection->update(
                    'user',
                    [
                        'is_active_flag' => 1,
                        'activation_code' => '',
                    ],
                    [
                        'user_id' => $activation['id']
                    ]
                );
                $tpl->assign('sucess', true);
            } else {
                if ($activation['code'] === '') {
                    $tpl->assign('errorAlreadyActivated', true);
                } else {
                    $tpl->assign('errorCode', true);
                }
            }
        } else {
            $tpl->assign('errorCode', true);
        }
    } else {
        $tpl->assign('errorEMail', true);
    }
}

$tpl->assign('email', $email);
$tpl->assign('code', $code);

$tpl->display();
