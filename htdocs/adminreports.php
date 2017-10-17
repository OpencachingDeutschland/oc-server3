<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'adminreports';
$tpl->menuitem = MNU_ADMIN_REPORTS;

$error = 0;

$login->verify();
if ($login->userid === 0) {
    $tpl->redirect_login();
}

if (($login->admin & ADMIN_USER) != ADMIN_USER) {
    $tpl->error(ERROR_NO_ACCESS);
}

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$id = (int) isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$rId = (int) isset($_REQUEST['rid']) ? $_REQUEST['rid'] : 0;
$cacheId = (int) isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;
$ownerId = (int) isset($_REQUEST['ownerid']) ? $_REQUEST['ownerid'] : 0;

$reportData = $connection
    ->fetchAssoc(
        'SELECT `userid`, `adminid`, DATEDIFF(NOW(),`lastmodified`) AS age 
         FROM `cache_reports`
         WHERE `id`= :id',
        ['id' => $rId]
    );

$reporterId = (int) $reportData['userid'];
$adminId = (int) $reportData['adminid'];
$age = $reportData['age'];

if (isset($_REQUEST['savecomment'])) {
    $comment = isset($_REQUEST['commenteditor']) ? $_REQUEST['commenteditor'] : '';
    $id = $rId;
    $connection->update(
        'cache_reports',
        ['comment' => $comment],
        ['id' => $id]
    );
} elseif (
    isset($_REQUEST['assign']) &&
    $rId > 0 &&
    ($adminId === 0 || $adminId === $login->userid || ($adminId !== $login->userid && $age >= 14))
) {
    $connection->update(
        'cache_reports',
        [
            'status' => 2,
            'adminid' => $login->userid,
        ],
        ['id' => $rId]
    );
    $tpl->redirect('adminreports.php?id=' . $rId);
} elseif (isset($_REQUEST['contact']) && $ownerId > 0) {
    $wp_oc = sql_value("SELECT `wp_oc` FROM `caches` WHERE `cache_id`='&1'", '', $cacheId);
    $tpl->redirect('mailto.php?userid=' . urlencode($ownerId) . '&wp=' . $wp_oc);
} elseif (isset($_REQUEST['contact_reporter']) && $reporterId > 0) {
    $tpl->redirect('mailto.php?userid=' . urlencode($reporterId) . '&reportid=' . $rId);
} elseif (isset($_REQUEST['done']) && $adminId == $login->userid) {
    sql("UPDATE `cache_reports` SET `status`=3 WHERE `id`=&1", $rId);
    $tpl->redirect('adminreports.php?id=' . $rId);
} elseif (isset($_REQUEST['assign']) && ($adminId === 0 || $adminId !== $login->userid)) {
    $error = 1;
    $id = 0;
    if ($rId > 0) {
        $id = $rId;
    }
} elseif (isset($_REQUEST['assign']) && $adminId === $login->userid) {
    $error = 2;
    $id = $rId;
} elseif (isset($_REQUEST['statusActive']) ||
    isset($_REQUEST['statusTNA']) ||
    isset($_REQUEST['statusArchived']) ||
    isset($_REQUEST['done']) ||
    isset($_REQUEST['statusLockedVisible']) ||
    isset($_REQUEST['statusLockedInvisible'])
) {
    if ($adminId === 0) {
        $id = $rId;
        $error = 4;
    } elseif ($adminId !== $login->userid) {
        $id = $rId;
        $error = 3;
    }
}

if ($id === 0) {
    // no details, show list of reported caches
    $rs = $connection->fetchAll(
        'SELECT `cr`.`id`,
                IF(`cr`.`status`=1,\'(*) \', \'\') AS `new`,
                `c`.`name`,
                `u2`.`username` AS `ownernick`,
                `u`.`username`,
                IF(LENGTH(`u3`.`username`)>10, CONCAT(LEFT(`u3`.`username`,9),\'.\'),`u3`.`username`) AS `adminname`,
                `cr`.`lastmodified`,
                `cr`.`adminid` IS NOT NULL AND `cr`.`adminid`!= :userId AS otheradmin
         FROM `cache_reports` `cr`
         INNER JOIN `caches` `c` ON `c`.`cache_id` = `cr`.`cacheid`
         INNER JOIN `user` `u` ON `u`.`user_id`  = `cr`.`userid`
         INNER JOIN `user` AS `u2` ON `u2`.`user_id`=`c`.`user_id`
         LEFT JOIN `user` AS `u3` ON `u3`.`user_id`=`cr`.`adminid`
         WHERE `cr`.`status` < 3
         ORDER BY (`cr`.`adminid` IS NULL OR `cr`.`adminid` = :userId) DESC,
                  `cr`.`status` ASC,
                  `cr`.`lastmodified` ASC',
        ['userId' => $login->userid]
    );

    $tpl->assign('reportedcaches', $rs);
    $tpl->assign('list', true);
} else {
    // show details of a report
    $record = $connection->fetchAssoc(
        'SELECT `cr`.`id`, `cr`.`cacheid`, `cr`.`userid`,
                `u1`.`username` AS `usernick`,
                IFNULL(`cr`.`adminid`, 0) AS `adminid`,
                IFNULL(`u2`.`username`, \'\') AS `adminnick`,
                IFNULL(`tt2`.`text`, `crr`.`name`) AS `reason`,
                `cr`.`note`,
                IFNULL(tt.text, crs.name) AS `status`,
                `cr`.`status`= :inProgress AS `inprogress`,
                `cr`.`status`= :done AS `closed`,
                `cr`.`date_created`, `cr`.`lastmodified`,
                `c`.`name` AS `cachename`,
                `c`.`user_id` AS `ownerid`,
                `cr`.`comment`,
                DATEDIFF(NOW(),`lastmodified`) AS `days_since_change`
         FROM `cache_reports` AS `cr`
         LEFT JOIN `cache_report_reasons` AS `crr` ON `cr`.`reason`=`crr`.`id`
         LEFT JOIN `caches` AS `c` ON `c`.`cache_id`=`cr`.`cacheid`
         LEFT JOIN `user` AS `u1` ON `u1`.`user_id`=`cr`.`userid`
         LEFT JOIN `user` AS `u2` ON `u2`.`user_id`=`cr`.`adminid`
         LEFT JOIN `cache_report_status` AS `crs` ON `cr`.`status`=`crs`.`id`
         LEFT JOIN `sys_trans_text` AS `tt` ON `crs`.`trans_id`=`tt`.`trans_id` AND `tt`.`lang`= :locale
         LEFT JOIN `sys_trans_text` AS `tt2` ON `crr`.`trans_id`=`tt2`.`trans_id` AND `tt2`.`lang`= :locale
         WHERE `cr`.`id`=  :id',
        [
            'id' => $id,
            'inProgress' => CACHE_REPORT_INPROGRESS,
            'done' => CACHE_REPORT_DONE,
            'locale' => $opt['template']['locale'],
        ]
    );

    if ($record) {
        $note = trim($record['note']);
        $note = nl2br(htmlentities($note));
        $note = preg_replace(
            "/\b(OC[0-9A-F]{4,6})\b/",
            "<a href='https://opencaching.de/$1' target='_blank'>$1</a>",
            $note
        );
        $note = preg_replace(
            "/\b(GC[0-9A-Z]{3,7})\b/",
            "<a href='https://www.geocaching.com/geocache/$1' target='_blank'>$1</a>",
            $note
        );

        $tpl->assign('id', $record['id']);
        $tpl->assign('cacheid', $record['cacheid']);
        $tpl->assign('userid', $record['userid']);
        $tpl->assign('usernick', $record['usernick']);
        $tpl->assign('adminid', $record['adminid']);
        $tpl->assign('adminnick', $record['adminnick']);
        $tpl->assign('reason', $record['reason']);
        $tpl->assign('note', $note);
        $tpl->assign('status', $record['status']);
        $tpl->assign('created', $record['date_created']);
        $tpl->assign('lastmodified', $record['lastmodified']);
        $tpl->assign(
            'reopenable',
            $record['adminid'] == $login->userid &&
            $record['closed'] == 1 &&
            $record['days_since_change'] <= 45
        );
        $tpl->assign('cachename', $record['cachename']);
        $tpl->assign('ownerid', $record['ownerid']);
        $tpl->assign('admin_comment', $record['comment']);
        if (isset($opt['logic']['adminreports']['cachexternal'])) {
            $tpl->assign('cachexternal', $opt['logic']['adminreports']['cachexternal']);
        } else {
            $tpl->assign('cachexternal', []);
        }

        if (isset($opt['logic']['adminreports']['external_maintainer'])) {
            $external_maintainer = @file_get_contents(
                mb_ereg_replace(
                    '%1',
                    $record['cacheid'],
                    $opt['logic']['adminreports']['external_maintainer']['url']
                )
            );
            if ($external_maintainer) {
                $tpl->assign(
                    'external_maintainer_msg',
                    mb_ereg_replace(
                        '%1',
                        htmlspecialchars($external_maintainer),
                        $opt['logic']['adminreports']['external_maintainer']['msg']
                    )
                );
            } else {
                $tpl->assign('external_maintainer_msg', false);
            }
        }
    }

    $tpl->assign('list', false);
    $tpl->assign('otheradmin', $record['adminid'] > 0 && $record['adminid'] != $login->userid);
    $tpl->assign('ownreport', $record['adminid'] == $login->userid);
    $tpl->assign('inprogress', $record['inprogress']);
    $otherReportInProgress = $connection->fetchColumn(
        'SELECT `id`
           FROM `cache_reports`
           WHERE `cacheid`= :cacheId AND `id`<> :id AND `status`= :reportInProgress
           LIMIT 1',
        [
            'cacheId' => $record['cacheid'],
            'id' => $record['id'],
            'reportInProgress' => CACHE_REPORT_INPROGRESS,
        ]
    );
    $tpl->assign('other_report_in_progress', $otherReportInProgress > 0);

    $cache = new cache($record['cacheid']);
    $cache->setTplHistoryData($id);
}

$tpl->assign('error', $error);
$tpl->display();
