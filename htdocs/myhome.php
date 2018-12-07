<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$tpl->name = 'myhome';
$tpl->menuitem = MNU_MYPROFILE_OVERVIEW;
$login->verify();

if ($login->userid == 0) {
    $tpl->redirect('login.php?target=myhome.php');
}

//get user record
$rUser = $connection->fetchAssoc(
    'SELECT IFNULL(`stat_user`.`found`, 0) AS `found`,
            IFNULL(`stat_user`.`hidden`, 0) AS `hidden`
     FROM `user`
     LEFT JOIN `stat_user`
       ON `user`.`user_id`=`stat_user`.`user_id`
     WHERE `user`.`user_id`= :userId LIMIT 1',
    [':userId' => $login->userid]
);

$tpl->assign('found', $rUser['found']);

// locked/hidden caches are visible for the user and must be added to public stats
$rUser['hidden'] += $connection->fetchColumn(
    'SELECT COUNT(*) FROM `caches` WHERE `user_id`= :userId AND `status` = 7',
    [':userId' => $login->userid]
);
$tpl->assign('hidden', $rUser['hidden']);

//get last logs
$logs = $connection->fetchAll(
    'SELECT SQL_CALC_FOUND_ROWS
             `cache_logs`.`cache_id` `cacheid`,
             `cache_logs`.`type` `type`,
             `cache_logs`.`date` `date`,
             `caches`.`name` `name`,
             `user`.`user_id` AS `userid`,
             `user`.`username`,
             `caches`.`wp_oc`,
             `ca`.`attrib_id` IS NOT NULL AS `oconly`,
             `cache_rating`.`rating_date` IS NOT NULL AND `cache_logs`.`type` IN (1,7) AS `recommended`,
             `cache_logs`.`oc_team_comment`,
             `cache_logs`.`needs_maintenance`,
             `cache_logs`.`listing_outdated`
         FROM `cache_logs`
         INNER JOIN `caches`
           ON `cache_logs`.`cache_id`=`caches`.`cache_id`
         INNER JOIN `user`
           ON `caches`.`user_id`=`user`.`user_id`
         LEFT JOIN `caches_attributes` `ca`
           ON `ca`.`cache_id`=`caches`.`cache_id`
           AND `ca`.`attrib_id`=6
         LEFT JOIN `cache_rating`
           ON `cache_rating`.`cache_id`=`caches`.`cache_id`
           AND `cache_rating`.`user_id`=`cache_logs`.`user_id` AND `cache_rating`.`rating_date`=`cache_logs`.`date`
         WHERE `cache_logs`.`user_id`= :userId
         ORDER BY `cache_logs`.`order_date` DESC, `cache_logs`.`date_created` DESC, `cache_logs`.`id` DESC
         LIMIT 10',
    [':userId' => $login->userid]
);

$tpl->assign('logs', $logs);
$tpl->assign('morelogs', $connection->fetchColumn("SELECT FOUND_ROWS()") > 10);

//get last hidden caches
$caches = $connection
    ->fetchAll(
        'SELECT `caches`.`cache_id`, `caches`.`name`, `caches`.`type`,
                    `caches`.`date_hidden`, `caches`.`status`, `caches`.`wp_oc`,
                    IF(`caches`.`needs_maintenance`, 2, 0) AS `needs_maintenance`,
                    IF(`caches`.`listing_outdated`, 2, 0) AS `listing_outdated`,
                    `stat_caches`.`found`,`stat_caches`.`toprating`,
                    `ca`.`attrib_id` IS NOT NULL AS `oconly`,
                    MAX(`cache_logs`.`date`) AS `lastlog`,
                    (SELECT `type` FROM `cache_logs` `cl2`
            WHERE `cl2`.`cache_id`=`caches`.`cache_id`
         ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC LIMIT 1) AS `lastlog_type`
             FROM `caches`
        LEFT JOIN `stat_caches` ON `stat_caches`.`cache_id`=`caches`.`cache_id`
        LEFT JOIN `cache_logs` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
        LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
            WHERE `caches`.`user_id`= :userId
              AND `caches`.`status` != 5
         GROUP BY `caches`.`cache_id`
         ORDER BY `lastlog` DESC,`caches`.`date_hidden` DESC, `caches`.`date_created` DESC',
        [':userId' => $login->userid]
    );
$tpl->assign('caches', $caches);

if ($useragent_msie && $useragent_msie_version < 9) {
    $tpl->assign('dotfill', '');
} else {
    $tpl->assign(
        'dotfill',
        '...........................................................................................................'
    );
}
$tpl->add_body_load('myHomeLoad()');

//get not published caches
$notPublished = $connection->fetchAll(
    'SELECT `caches`.`cache_id`,
                `caches`.`name`,
                `caches`.`date_hidden`,
                `caches`.`date_activate`,
                `caches`.`status`,
                `caches`.`wp_oc`,
                `caches`.`type`,
                `ca`.`attrib_id` IS NOT NULL AS `oconly`
         FROM `caches`
         LEFT JOIN `caches_attributes` `ca`
           ON `ca`.`cache_id`=`caches`.`cache_id`
           AND `ca`.`attrib_id`=6
         WHERE `user_id`= :userId
           AND `caches`.`status` = 5
         ORDER BY `date_activate` DESC, `caches`.`date_created` DESC',
    [':userId' => $login->userid]
);
$tpl->assign('notpublished', $notPublished);

// get number of sent emails
// useless information when email protocol is cleaned-up (cronjob 'purge_logs')
// $tpl->assign('emails', sql_value("SELECT COUNT(*) FROM `email_user` WHERE `from_user_id`='&1'", 0, $login->userid));

// get log pictures
$allpics = isset($_REQUEST['allpics']) ? $_REQUEST['allpics'] : false;
if ($allpics === '1') {
    // downward compatibility for external or bookmarked links, see redmine #39 change
    $allpics = 'ownlogs';
}
if ($allpics == 'ownlogs' || $allpics == 'owncaches') {
    $gallery = ($allpics == 'ownlogs' ? LogPics::FOR_OWNLOGS_GALLERY : LogPics::FOR_OWNCACHES_GALLERY);
    $all_pictures = LogPics::get($gallery);
    LogPics::setPaging($gallery, 0, 0, "myhome.php?allpics=" . $allpics);
} else {
    $all_pictures = LogPics::get(LogPics::FOR_OWNLOGS_GALLERY);
    $tpl->assign('pictures', $all_pictures);
}
$tpl->assign('allpics', $allpics);
$tpl->assign('total_pictures', count($all_pictures));

// display
$tpl->display();
