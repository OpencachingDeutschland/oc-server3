<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$tpl->name = 'viewprofile';
$tpl->menuitem = MNU_CACHES_USERPROFILE;

$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] + 0 : 0;
$allpics = isset($_REQUEST['allpics']) ? $_REQUEST['allpics'] + 0 : 0;

if ($userid == 0) {
    $login->verify();
    if ($login->userid != 0) {
        // 'show public profile' in my-profile menu
        $userid = $login->userid;
        $tpl->menuitem = MNU_MYPROFILE_PUBLIC;
    }
}

// user data and basic statistics
$record = $connection->fetchAssoc(
    'SELECT `user`.`username`,
            `user`.`last_login`,
            `user`.`accept_mailing`,
            `user`.`pmr_flag`,
            `user`.`date_created`,
            `user`.`password`,
            `user`.`email`,
            `user`.`is_active_flag`,
            `user`.`latitude`,
            `user`.`longitude`,
            `user`.`data_license`,
            IFNULL(`sys_trans_text`.`text`,`countries`.`name`) AS `country`,
            `stat_user`.`hidden`,
            `stat_user`.`found`,
            `stat_user`.`notfound`,
            `stat_user`.`note`,
            `stat_user`.`maintenance`,
            `user`.`uuid`
     FROM `user`
     LEFT JOIN `stat_user`
       ON `user`.`user_id`=`stat_user`.`user_id`
     LEFT JOIN `countries`
       ON `user`.`country`=`countries`.`short`
     LEFT JOIN `sys_trans_text`
       ON `sys_trans_text`.`lang`= :language
       AND `sys_trans_text`.`trans_id`=`countries`.`trans_id`
     WHERE `user`.`user_id`= :userId',
    [
        ':userId' => $userid,
        ':language' => $opt['template']['locale'],
    ]
);

if (!is_array($record)) {
    $tpl->error(ERROR_USER_NOT_EXISTS);
}

$active = $connection->fetchColumn(
    'SELECT COUNT(*) FROM `caches` WHERE `user_id`= :userId AND `status` = 1',
    [':userId' => $userid]
);

$rs = $connection->fetchAll(
    'SELECT IFNULL(`tt`.`text`, `p`.`name`) AS `name`,
            `u`.`option_value`, `u`.`option_id` AS `option_id`
     FROM `profile_options` AS `p`
     LEFT JOIN `user_options` AS `u`
       ON `p`.`id`=`u`.`option_id`
     LEFT JOIN `sys_trans` AS `st`
       ON `st`.`id`=`p`.`trans_id`
       AND `st`.`text`=`p`.`name`
     LEFT JOIN `sys_trans_text` AS `tt`
       ON `st`.`id`=`tt`.`trans_id`
       AND `tt`.`lang` = :language
     WHERE `u`.`option_visible`=1
       AND `p`.`internal_use`=0
       AND `u`.`user_id`= :userId
     ORDER BY `p`.`option_order`',
    [
        ':userId' => $userid,
        ':language' => $opt['template']['locale'],
    ]
);

$tpl->assign('useroptions', $rs);

$userDescription = $connection->fetchColumn(
    'SELECT `description` FROM `user` WHERE `user_id`= :userId',
    [':userId' => $userid]
);

$tpl->assign('description', use_current_protocol_in_html($userDescription));

$useropt = new useroptions($userid);
$show_statistics = $useropt->getOptValue(USR_OPT_SHOWSTATS);
$show_oconly81 = $useropt->getOptValue(USR_OPT_OCONLY81);
if ($show_oconly81) {
    $tpl->assign('oco81_helplink', helppagelink('oconly81'));
}

if ($show_statistics) {
    // detail statistics
    $rs = $connection->fetchAll(
        'SELECT COUNT(*) AS `anzahl`, `t`.`id`,
                IFNULL(`tt`.`text`, `t`.`name`) AS `cachetype`
         FROM `caches` AS `c`
         LEFT JOIN `cache_type` AS `t`
           ON `t`.`id`=`c`.`type`
         LEFT JOIN `sys_trans` AS `st`
           ON `st`.`id`=`t`.`trans_id`
           AND `t`.`name`=`st`.`text`
         LEFT JOIN `sys_trans_text` AS `tt`
           ON `st`.`id`=`tt`.`trans_id`
           AND `tt`.`lang`= :language
         LEFT JOIN `cache_status`
           ON `cache_status`.`id`=`c`.`status`
         WHERE `c`.`user_id`= :userId
           AND `allow_user_view`= 1
         GROUP BY `t`.`id`
         ORDER BY `anzahl` DESC, `t`.`ordinal` ASC',
        [
            ':userId' => $userid,
            ':language' => $opt['template']['locale'],
        ]
    );
    $tpl->assign('userstatshidden', $rs);

    $rs = $connection->fetchAll(
        'SELECT COUNT(*) AS `anzahl`, `t`.`id`, IFNULL(`tt`.`text`, `t`.`name`) AS `cachetype`
         FROM `cache_logs` AS `l`
         LEFT JOIN `caches` AS `c`
           ON `l`.`cache_id`=`c`.`cache_id`
         LEFT JOIN `cache_type` AS `t`
           ON `t`.`id`=`c`.`type`
         LEFT JOIN `sys_trans` AS `st`
           ON `st`.`id`=`t`.`trans_id`
           AND `t`.`name`=`st`.`text`
         LEFT JOIN `sys_trans_text` AS `tt`
           ON `st`.`id`=`tt`.`trans_id`
           AND `tt`.`lang`= :language
         WHERE `l`.`user_id`= :userId
           AND (`l`.`type`=1 OR `l`.`type`=7)
         GROUP BY `t`.`id`
         ORDER BY `anzahl` DESC, `t`.`ordinal` ASC',
        [
            ':userId' => $userid,
            ':language' => $opt['template']['locale'],
        ]
    );
    $tpl->assign('userstatsfound', $rs);

    $rs = $connection->fetchAll(
        'SELECT COUNT(*) AS `count`,
                IFNULL(`stt`.`text`, `caches`.`country`) AS `country`,
                IF(`caches`.`country`= :country AND `cache_location`.`code1`= :country, `cache_location`.`adm2`, NULL) AS `state`,
                `caches`.`country` AS `countrycode`,
                `cache_location`.`code2` AS `adm2code`
         FROM `cache_logs`
         INNER JOIN `caches`
           ON `caches`.`cache_id`=`cache_logs`.`cache_id`
         INNER JOIN `cache_location`
           ON `cache_location`.`cache_id`=`cache_logs`.`cache_id`
         LEFT JOIN `countries`
           ON `countries`.`short`=`caches`.`country`
         LEFT JOIN `sys_trans_text` `stt`
           ON `stt`.`lang`= :language
           AND `stt`.`trans_id`=`countries`.`trans_id`
         LEFT JOIN `caches_attributes` `ca`
           ON `ca`.`cache_id`=`caches`.`cache_id`
           AND `ca`.`attrib_id`=61
         WHERE `cache_logs`.`user_id`= :userId
           AND `cache_logs`.`type` IN (1,7)
           AND `ca`.`attrib_id` IS NULL
         GROUP BY `country`, `state`
         ORDER BY `count` DESC, `country`, `state`',
        [
            ':userId' => $userid,
            ':language' => $opt['template']['locale'],
            ':country' => $login->getUserCountry(),
        ]
    );

    $tpl->assign('regionstat', $rs);
}

// OConly statistics
$ocOnlyHidden = $connection->fetchColumn(
    'SELECT COUNT(*)
     FROM `caches`
     INNER JOIN `caches_attributes`
       ON `caches_attributes`.`cache_id`=`caches`.`cache_id`
       AND `caches_attributes`.`attrib_id`=6
     INNER JOIN `cache_status`
       ON `cache_status`.`id`=`caches`.`status`
       AND `allow_user_view`=1
     WHERE `user_id`= :userId',
    [':userId' => $userid]
);

$ocOnlyHiddenActive = $connection->fetchColumn(
    'SELECT COUNT(*)
     FROM `caches`
     INNER JOIN `caches_attributes`
       ON `caches_attributes`.`cache_id`=`caches`.`cache_id`
       AND `caches_attributes`.`attrib_id`= 6
     WHERE `user_id`= :userId
       AND `caches`.`status`= 1',
    [':userId' => $userid]
);
$ocOnlyRecommended = $connection->fetchColumn(
    'SELECT COUNT(*)
     FROM `cache_logs`
     INNER JOIN `caches_attributes`
       ON `caches_attributes`.`cache_id`=`cache_logs`.`cache_id`
       AND `caches_attributes`.`attrib_id`=6
     INNER JOIN `cache_rating`
       ON `cache_rating`.`user_id`=`cache_logs`.`user_id`
       AND `cache_rating`.`cache_id`=`cache_logs`.`cache_id`
       AND `cache_rating`.`rating_date`=`cache_logs`.`date`
     WHERE `cache_logs`.`user_id`= :userId
       AND `cache_logs`.`type` IN (1,7)',
    [':userId' => $userid]
);

$rs = $connection->fetchAll(
    'SELECT COUNT(*) AS `count`,
            IFNULL(`stt`.`text`, `caches`.`country`) AS `country`,
            IF(`caches`.`country`= :country AND `cache_location`.`code1`= :country, `cache_location`.`adm2`, NULL) AS `state`,
            `caches`.`country` AS `countrycode`,
            `cache_location`.`code2` AS `adm2code`
     FROM `cache_logs`
     INNER JOIN `caches_attributes`
       ON `caches_attributes`.`cache_id`=`cache_logs`.`cache_id`
       AND `caches_attributes`.`attrib_id`=6
     INNER JOIN `caches`
       ON `caches`.`cache_id`=`cache_logs`.`cache_id`
     INNER JOIN `cache_location`
       ON `cache_location`.`cache_id`=`cache_logs`.`cache_id`
     LEFT JOIN `countries`
       ON `countries`.`short`=`caches`.`country`
     LEFT JOIN `sys_trans_text` `stt`
       ON `stt`.`lang`= :language
       AND `stt`.`trans_id`=`countries`.`trans_id`
     LEFT JOIN `caches_attributes` `ca`
       ON `ca`.`cache_id`=`caches`.`cache_id`
       AND `ca`.`attrib_id`=61
     WHERE `cache_logs`.`user_id`= :userId
       AND `cache_logs`.`type` IN (1,7)
       AND `ca`.`attrib_id` IS NULL
     GROUP BY `country`, `state`
     ORDER BY `count` DESC, `country`, `state`',
    [
        ':userId' => $userid,
        ':language' => $opt['template']['locale'],
        ':country' => $login->getUserCountry(),
    ]
);

$tpl->assign('oconly_regionstat', $rs);

$rs = $connection->fetchAll(
    'SELECT `cache_logs`.`type`,
            COUNT(*) AS `count`
     FROM `cache_logs`
     INNER JOIN `caches_attributes`
       ON `caches_attributes`.`cache_id`=`cache_logs`.`cache_id`
       AND `caches_attributes`.`attrib_id`=6
     WHERE `user_id`= :userId
     GROUP BY `cache_logs`.`type`',
    [':userId' => $userid]
);
$oconly_found = 0;
$oconly_dnf = 0;
$oconly_note = 0;
$oconly_maint = 0;

foreach ($rs as $r) {
    switch ($r['type']) {
        case 1:
        case 7:
            $oconly_found += $r['count'];
            break;
        case 2:
            $oconly_dnf = $r['count'];
            break;
        case 3:
            $oconly_note = $r['count'];
            break;
        case 9:
        case 10:
        case 11:
        case 13:
            $oconly_maint += $r['count'];
            break;
    }
}

$tpl->assign('oconly_found', $oconly_found);
$tpl->assign('oconly_dnf', $oconly_dnf);
$tpl->assign('oconly_note', $oconly_note);
$tpl->assign('oconly_maint', $oconly_maint);

// OConly detail statistics
if ($show_statistics) {
    $rs = $connection->fetchAll(
        'SELECT COUNT(*) AS `anzahl`, `t`.`id`,
                IFNULL(`tt`.`text`, `t`.`name`) AS `cachetype`
         FROM `caches` AS `c`
         LEFT JOIN `cache_type` AS `t`
           ON `t`.`id`=`c`.`type`
         LEFT JOIN `sys_trans` AS `st`
           ON `st`.`id`=`t`.`trans_id`
           AND `t`.`name`=`st`.`text`
         LEFT JOIN `sys_trans_text` AS `tt`
           ON `st`.`id`=`tt`.`trans_id`
           AND `tt`.`lang`= :language
         LEFT JOIN `cache_status`
           ON `cache_status`.`id`=`c`.`status`
         INNER JOIN `caches_attributes`
           ON `caches_attributes`.`cache_id`=`c`.`cache_id`
           AND `caches_attributes`.`attrib_id`=6
         WHERE `c`.`user_id`= :userId
           AND `allow_user_view`= 1
         GROUP BY `t`.`id`
         ORDER BY `anzahl` DESC, `t`.`ordinal` ASC',
        [
            ':userId' => $userid,
            ':language' => $opt['template']['locale'],
        ]
    );
    $tpl->assign('oconly_userstatshidden', $rs);

    $rs = $connection->fetchAll(
        'SELECT COUNT(*) AS `anzahl`,
                `t`.`id`, IFNULL(`tt`.`text`, `t`.`name`) AS `cachetype`
         FROM `cache_logs` AS `l`
         LEFT JOIN `caches` AS `c`
           ON `l`.`cache_id`=`c`.`cache_id`
         LEFT JOIN `cache_type` AS `t`
           ON `t`.`id`=`c`.`type`
         LEFT JOIN `sys_trans` AS `st`
           ON `st`.`id`=`t`.`trans_id`
           AND `t`.`name`=`st`.`text`
         LEFT JOIN `sys_trans_text` AS `tt`
           ON `st`.`id`=`tt`.`trans_id`
           AND `tt`.`lang`= :language
         INNER JOIN `caches_attributes`
           ON `caches_attributes`.`cache_id`=`c`.`cache_id`
           AND `caches_attributes`.`attrib_id`=6
         WHERE `l`.`user_id`= :userId
           AND (`l`.`type`=1 OR `l`.`type`=7)
         GROUP BY `t`.`id`
         ORDER BY `anzahl` DESC, `t`.`ordinal` ASC',
        [
            ':userId' => $userid,
            ':language' => $opt['template']['locale'],
        ]
    );
    $tpl->assign('oconly_userstatsfound', $rs);
}

if ($show_oconly81) {
    require __DIR__ . '/lib2/logic/oconly81.inc.php';
    set_oconly81_tpldata($userid);
}

$menu->SetSelectItem($tpl->menuitem);

$tpl->title = $menu->GetMenuTitle() . ' ' . $record['username'];

$user = new user($userid);
$ratingParams = $user->getRatingParameters();

$tpl->assign('username', $record['username']);
$tpl->assign('userid', $userid);
$tpl->assign('uuid', $record['uuid']);
$tpl->assign('founds', $record['found'] <= 0 ? '0' : $record['found']);
$tpl->assign('notfound', $record['notfound'] <= 0 ? '0' : $record['notfound']);
$tpl->assign('note', $record['note'] <= 0 ? '0' : $record['note']);
$tpl->assign('maintenance', $record['maintenance'] <= 0 ? '0' : $record['maintenance']);
$tpl->assign('hidden', $record['hidden'] <= 0 ? '0' : $record['hidden']);
$tpl->assign('active', $active);
$tpl->assign('recommended', $ratingParams['givenRatings']);
$tpl->assign('maxRecommended', $ratingParams['maxRatings']);
$tpl->assign('show_statistics', $show_statistics);
$tpl->assign('show_oconly81', $show_oconly81);

$tpl->assign('ocOnlyHidden', $ocOnlyHidden);
$tpl->assign('ocOnlyHiddenActive', $ocOnlyHiddenActive);
$tpl->assign('ocOnlyRecommended', $ocOnlyRecommended);

$picstat = ($useropt->getOptValue(USR_OPT_PICSTAT) == 1) && !$user->getLicenseDeclined();
$tpl->assign('show_picstat', $picstat);
if ($picstat) {
    // user has allowed picture stat and gallery view
    $tpl->assign('allpics', $allpics);
    if (!$allpics) {
        $tpl->assign('logpics', LogPics::get(LogPics::FOR_USER_STAT, $userid));
    } else {
        LogPics::setPaging(LogPics::FOR_USER_GALLERY, $userid, 0, "viewprofile.php?userid=" . $userid . "&allpics=1");
        $tpl->name = 'viewprofile_pics';
        // actually we don't need all the other stuff here ..
    }
}

$tpl->assign('showcountry', (strlen(trim($record['country'])) > 0));
$tpl->assign('country', $record['country']);
$tpl->assign('registered', $record['date_created']);

/* set last_login to one of 5 categories
 *   1 = this month or last month
 *   2 = between one and 6 months
 *   3 = between 6 and 12 months
 *   4 = between 12 and 24 months
 *   5 = more than 12 months
 *   6 = unknown, we need this, because we don't
 *       know the last_login of all accounts.
 *       Can be removed after one year.
 *   7 = user account is not active (disabled)
 */
if ($record['password'] == null || $record['email'] == null || $record['is_active_flag'] != 1) {
    $tpl->assign('lastlogin', 7);
} else {
    if ($record['last_login'] === null) {
        $tpl->assign('lastlogin', 6);
    } else {
        $record['last_login'] = strtotime($record['last_login']);
        $record['last_login'] = mktime(
            date('G', $record['last_login']),
            date('i', $record['last_login']),
            date('s', $record['last_login']),
            date('n', $record['last_login']),
            date(1, $record['last_login']),
            date('Y', $record['last_login'])
        );
        if ($record['last_login'] >= mktime(0, 0, 0, date("m") - 1, 1, date("Y"))) {
            $tpl->assign('lastlogin', 1);
        } else {
            if ($record['last_login'] >= mktime(0, 0, 0, date("m") - 6, 1, date("Y"))) {
                $tpl->assign('lastlogin', 2);
            } else {
                if ($record['last_login'] >= mktime(0, 0, 0, date("m") - 12, 1, date("Y"))) {
                    $tpl->assign('lastlogin', 3);
                } else {
                    if ($record['last_login'] >= mktime(0, 0, 0, date("m") - 24, 1, date("Y"))) {
                        $tpl->assign('lastlogin', 4);
                    } else {
                        $tpl->assign('lastlogin', 5);
                    }
                }
            }
        }
    }
}

$tpl->assign('license_actively_declined', $record['data_license'] == NEW_DATA_LICENSE_ACTIVELY_DECLINED);
$tpl->assign('license_passively_declined', $record['data_license'] == NEW_DATA_LICENSE_PASSIVELY_DECLINED);
$tpl->assign('accMailing', $record['accept_mailing']);
$tpl->assign('pmr', $record['pmr_flag']);

if (isset($_REQUEST['watchlist'])) {
    $list = new cachelist($_REQUEST['watchlist'] + 0);
    if ($list->exist()) {
        $list->watch(true);
    }
} else {
    if (isset($_REQUEST['dontwatchlist'])) {
        $list = new cachelist($_REQUEST['dontwatchlist'] + 0);
        if ($list->exist()) {
            $list->watch(false);
        }
    }
}

$tpl->assign('cachelists', cachelist::getPublicListsOf($userid));
$tpl->assign('show_bookmarks', true);

$tpl->assign('tdummy', time());
// Dummy counter is needed to make consecutive clicks on the same link work.

$tpl->display();
