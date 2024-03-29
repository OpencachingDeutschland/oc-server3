<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

$login->verify();
$tpl->name = 'change_statpic';
$tpl->menuitem = MNU_MYPROFILE_DATA_STATPIC;

if ($login->userid == 0) {
    $tpl->redirect('login.php?target=change_statpic.php');
}

if (isset($_REQUEST['cancel'])) {
    $tpl->redirect('mystatpic.php');
}

$sp = new statpic($login->userid);

if (isset($_REQUEST['ok'])) {
    $bError = false;

    if (isset($_REQUEST['statpic_text'])) {
        if (!$sp->setText($_REQUEST['statpic_text'])) {
            $bError = true;
            $tpl->assign('statpic_text_error', 1);
        }
    }

    if (isset($_REQUEST['statpic_style'])) {
        $sp->setStyle($_REQUEST['statpic_style']);
    }

    if (!$bError) {
        $sp->save();
        $tpl->redirect('mystatpic.php');
    }
}

$tpl->assign('statpic_text', isset($_REQUEST['statpic_text']) ? $_REQUEST['statpic_text'] : $sp->getText());
$tpl->assign('statpic_style', isset($_REQUEST['statpic_style']) ? $_REQUEST['statpic_style'] : $sp->getStyle());

/** @var Doctrine\DBAL\Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);
$rs = $connection->fetchAllAssociative(
    'SELECT `statpics`.`id`,
            `statpics`.`previewpath`,
            IFNULL(`sys_trans_text`.`text`, `statpics`.`description`) AS `description`
     FROM `statpics`
     LEFT JOIN `sys_trans_text`
         ON `statpics`.`trans_id`=`sys_trans_text`.`trans_id`
         AND `sys_trans_text`.`lang`= :lang
     ORDER BY `statpics`.`id` ASC',
    ['lang' => $opt['template']['locale']]
);

$tpl->assign('statpics', $rs);

$tpl->display();
