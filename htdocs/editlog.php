<?php
/****************************************************************************
 * for license information see LICENSE.md
 *
 *  edit a cache log
 *
 *  used template(s): editlog
 *  GET/POST Parameter: logid
 *
 *  Note: when changing recommendation, the last_modified-date of log-record
 *        has to be updated to trigger resync via xml-interface
 *
 *****************************************************************************/

use OcLegacy\GeoCache\Recommendation;
use Oc\GeoCache\StatisticPicture;

require __DIR__ . '/lib2/web.inc.php';
require_once __DIR__ . '/lib2/logic/user.class.php';
require_once __DIR__ . '/lib2/edithelper.inc.php';

$tpl->name = 'log_cache';
$tpl->menuitem = MNU_CACHES_EDITLOG;
$tpl->caching = false;

// check login
$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}
$user = new user($login->userid);
$useradmin = ($login->hasAdminPriv() ? 1 : 0);

// fetch log entry
$log_id = 0;
if (isset($_REQUEST['logid'])) { // Ocprop
    $log_id = $_REQUEST['logid'];
}

$rs = sql('SELECT `id` FROM `log_types` WHERE `maintenance_logs`');
$logtype_allows_nm = sql_fetch_column($rs);

$log_rs = sql(
    "
        SELECT
            `cache_logs`.`id` AS `log_id`,
            `cache_logs`.`cache_id`,
            `cache_logs`.`node`,
            `cache_logs`.`text`,
            `cache_logs`.`date`,
            `cache_logs`.`needs_maintenance`,
            `cache_logs`.`listing_outdated`,
            `cache_logs`.`user_id`,
            `cache_logs`.`type` AS `logtype`,
            `cache_logs`.`oc_team_comment`,
            `cache_logs`.`text_html`,
            `cache_logs`.`text_htmledit`,
            `caches`.`name` AS `cachename`,
            `caches`.`type` AS `cachetype`,
            `caches`.`user_id` AS `cache_user_id`,
            `caches`.`logpw` AS `logpw`,
            `caches`.`status` AS `status`,
            `log_types`.`cache_status` > 0 AS `is_status_log`
        FROM `cache_logs`
        JOIN `log_types` ON `log_types`.`id`=`cache_logs`.`type`
        INNER JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
        WHERE `cache_logs`.`id`='&1'",
    $log_id
);
$log_record = sql_fetch_array($log_rs);
sql_free_result($log_rs);

// catch errors
if ($log_record === false) {
    $tpl->error(ERROR_INVALID_OPERATION);
}
if ($log_record['user_id'] != $login->userid ||
    ($log_record['status'] == 6 && $log_record['cache_user_id'] != $login->userid && !$useradmin) ||
    ($log_record['status'] == 7 && !$useradmin)
) {
    $tpl->error(ERROR_NO_ACCESS);
}
if ($log_record['node'] != $opt['logic']['node']['id']) {
    $tpl->error(ERROR_WRONG_NODE);
}

// load cache data
$cache = new cache($log_record['cache_id']);

// process url parametes
// Ocprop: logtype, logday, logmonth, logyear, rating, submitform
$log_type = isset($_POST['logtype']) ? $_POST['logtype'] : $log_record['logtype'];
$log_date_day =
    isset($_POST['logday']) ? trim($_POST['logday']) : date('d', strtotime($log_record['date']));
$log_date_month =
    isset($_POST['logmonth']) ? trim($_POST['logmonth']) : date('m', strtotime($log_record['date']));
$log_date_year =
    isset($_POST['logyear']) ? trim($_POST['logyear']) : date('Y', strtotime($log_record['date']));
$log_time_hour =
    isset($_POST['loghour'])
    ? trim($_POST['loghour'])
    : (substr($log_record['date'], 11) == '00:00:00' ? '' : date('H', strtotime($log_record['date'])));
$log_time_minute =
    isset($_POST['logminute']) 
    ? trim($_POST['logminute'])
    : (substr($log_record['date'], 11) == "00:00:00" ? "" : date('i', strtotime($log_record['date'])));
$top_option = isset($_POST['ratingoption']) ? $_POST['ratingoption'] + 0 : 0;
$top_cache = isset($_POST['rating']) ? $_POST['rating'] + 0 : 0;
$log_pw = isset($_POST['log_pw']) ? $_POST['log_pw'] : '';

if (isset($_POST['submitform']) ||
    (
        isset($_POST['oldDescMode']) && isset($_POST['descMode'])
        && $_POST['oldDescMode'] != $_POST['descMode']
    )
) {
    $oc_team_comment = isset($_POST['teamcomment']) ? $_POST['teamcomment'] != '' : false;
    $needsMaintenance = isset($_POST['needs_maintenance2']) ? $_POST['needs_maintenance2'] + 0 : (isset($_POST['needs_maintenance']) ? $_POST['needs_maintenance'] + 0 : 0);
    $listingOutdated = isset($_POST['listing_outdated2']) ? $_POST['listing_outdated2'] + 0 : (isset($_POST['listing_outdated']) ? $_POST['listing_outdated'] + 0 : 0);
    $confirmListingOk = isset($_POST['confirm_listing_ok']) ? $_POST['confirm_listing_ok'] + 0 : 0;

    // validate NM and LO flags
    if (!in_array($log_type, $logtype_allows_nm) || $cache->getType() == 6) {
        $needsMaintenance = $listingOutdated = 0;
    } else {
        if ($needsMaintenance != 1 && $needsMaintenance != 2) {
            $needsMaintenance = 0;
        }
        if ($listingOutdated != 1 && $listingOutdated != 2) {
            $listingOutdated = 0;
        }
    }
} else {
    $oc_team_comment = ($log_record['oc_team_comment'] == 1);
    $needsMaintenance = $log_record['needs_maintenance'];
    $listingOutdated = $log_record['listing_outdated'];
    $confirmListingOk = ($listingOutdated == 1);
}

// do not ask for PW again if it was alredy supplied when submitting the log 
$use_log_pw = $log_record['logpw'] != '' && $log_record['logtype'] != 1;

// editor mode switching
if (isset($_POST['descMode'])) {
    $descMode = $_POST['descMode'] + 0; // Ocprop: 2
    if (($descMode < 1) || ($descMode > 3)) {
        $descMode = 3;
    }
    if (isset($_POST['oldDescMode'])) {
        $oldDescMode = $_POST['oldDescMode'];
        if (($oldDescMode < 1) || ($oldDescMode > 3)) {
            $oldDescMode = $descMode;
        }
    } else {
        $oldDescMode = $descMode;
    }
} else {
    if ($log_record['text_html'] == 1) {
        if ($log_record['text_htmledit'] == 1) {
            $descMode = 3;
        } else {
            $descMode = 2;
        }
    } else {
        $descMode = 1;
    }

    $oldDescMode = $descMode;
}

// Text from textarea; Ocprop
if (isset($_POST['logtext'])) {
    $log_text = trim($_POST['logtext']);
} else {
    $log_text = $log_record['text'];
    if ($descMode == 1) {
        $oldDescMode = 0;
    }   // plain text with encoded HTML entities
}

$log_text = processEditorInput($oldDescMode, $descMode, $log_text, $represent_text);

// validate input
$validate = [];

$validate['dateOk'] = cachelog::validateDate(
    $log_date_year, $log_date_month, $log_date_day,
    $log_time_hour, $log_time_minute,
    isset($_POST['submitform'])
);

$validate['logType'] = logtype_ok($log_record['cache_id'], $log_type, $log_record['logtype']);

// not a found log? then ignore the recommendation
if ($log_type != 1 && $log_type != 7) {
    $top_option = 0;
}

// validate log password
if ($use_log_pw && $log_type == 1 && isset($_POST['submitform'])) {
    $validate['logPw'] = $cache->validateLogPW($log_type, $log_pw);
} else {
    $validate['logPw'] = true;
}

// ignore unauthorized team comments
if (!teamcomment_allowed($log_record['cache_id'], $log_type, $log_record['oc_team_comment'])) {
    $oc_team_comment = 0;
}

$validate['confirmListingOk'] =
    $listingOutdated != 1 || $confirmListingOk || $log_record['listing_outdated'] == 1 ||
    !$cache->getListingOutdatedRelativeToLog($log_id);

// check for errors
$loggable = array_product($validate);

// store?
if ($loggable && isset($_POST['submitform'])) { // Ocprop
    // 00:00:01 = "00:00 was logged"
    // 00:00:00 = "no time was logged"
    if ("$log_time_hour$log_time_minute" != "" &&
        $log_time_hour == 0 && $log_time_minute == 0
    ) {
        $log_time_second = 1;
    } else {
        $log_time_second = 0;
    }

    $log_date = date(
        'Y-m-d H:i:s',
        mktime(
            $log_time_hour + 0,
            $log_time_minute + 0,
            $log_time_second,
            $log_date_month,
            $log_date_day,
            $log_date_year
        )
    );

    // evtl. discard cache recommendation if the log type was changed from
    // 'found' or 'attended' to something else
    if (!$top_option) {
        Recommendation::discardRecommendation($log_id);
    }

    // store changed data
    sql(
        "UPDATE `cache_logs`
         SET `type`='&1',
             `oc_team_comment`='&2',
             `date`='&3',
             `needs_maintenance`='&4',
             `listing_outdated`='&5',
             `text`='&6',
             `text_html`='&7',
             `text_htmledit`='&8'
         WHERE `id`='&9'",
        $log_type,
        $oc_team_comment,
        $log_date,
        $needsMaintenance,
        $listingOutdated,
        $log_text,
        (($descMode != 1) ? 1 : 0),
        (($descMode == 3) ? 1 : 0),
        $log_id
    );

    // Update cache status if changed by logtype. To keep things simple, we implement
    // this feature only for the latest log.
    $statusChangeAllowed = $cache->statusChangeAllowedForLog($log_record['log_id']);
    if ($statusChangeAllowed) {
        $cache->updateCacheStatusFromLatestLog($log_id, $log_record['logtype'], $log_type);
        $cache->save();
    }

    // update user-stat if type changed
    if ($log_record['logtype'] != $log_type) {
        StatisticPicture::deleteStatisticPicture($user->getUserId());
    }

    // update recommendation list
    if ($top_option) {
        if ($top_cache) {
            sql(
                "INSERT INTO `cache_rating` (`user_id`, `cache_id`, `rating_date`)
                 VALUES('&1','&2','&3')
                 ON DUPLICATE KEY UPDATE `rating_date`='&3'",
                $user->getUserId(),
                $log_record['cache_id'],
                $log_date
            );
            // cache_rating.rating_date is updated when it already exists, so that
            // it stays consistent with cache_logs.date when editing a log date.

            // When editing one of multiple found logs, this will move rating_date
            // to the last edited record. While this may not always be what the user
            // expects, it makes sense for two reasons:
            //   1. It is a safeguard for the case that the log date and rating_date
            //      have gotten out of sync for some reason (which has happend in the
            //      past, probably due to a log-deletion related bug).
            //   2. It can be used as a tweak to control which log's date is relevant
            //      for the rating, e.g. when logging a second found on a recycled or
            //      renewed cache [listing].
        } else {
            sql(
                "DELETE FROM `cache_rating` WHERE `user_id`='&1' AND `cache_id`='&2'",
                $user->getUserId(),
                $log_record['cache_id']
            );
        }
    }

    // display cache page
    $tpl->redirect(
        'viewcache.php?cacheid=' . urlencode($log_record['cache_id'])
        . '&log=A#log' . urlencode($log_id)
    );
    exit;
}


// build logtype options
$disable_statuschange = !$cache->statusChangeAllowedForLog($log_record['log_id']);
$disable_typechange = $disable_statuschange && $log_record['is_status_log'];
$tpl->assign('typeEditDisabled', $disable_typechange);

$tpl->assign('validate', $validate);

// cache data
$tpl->assign('cacheid', $log_record['cache_id']);
$tpl->assign('cachename', htmlspecialchars($cache->getName(), ENT_COMPAT, 'UTF-8'));
$tpl->assign('cachetype', $cache->getType());
$tpl->assign('gcwp', $cache->getWPGC_maintained());

// log entry data
$tpl->assign('logid', $log_id);

$tpl->assign('logtypes', $cache->getUserLogTypes($log_type, $log_record['logtype'], !$disable_statuschange));
$tpl->assign('logday', htmlspecialchars($log_date_day, ENT_COMPAT, 'UTF-8'));
$tpl->assign('logmonth', htmlspecialchars($log_date_month, ENT_COMPAT, 'UTF-8'));
$tpl->assign('logyear', htmlspecialchars($log_date_year, ENT_COMPAT, 'UTF-8'));
$tpl->assign('loghour', htmlspecialchars($log_time_hour, ENT_COMPAT, 'UTF-8'));
$tpl->assign('logminute', htmlspecialchars($log_time_minute, ENT_COMPAT, 'UTF-8'));
$tpl->assign('logtext', $represent_text);

// admin
$tpl->assign('octeamcommentallowed', $cache->teamcommentAllowed(3, $log_record['oc_team_comment']));
$tpl->assign('is_teamcomment', $oc_team_comment);
$tpl->assign('adminAction', $user->getUserId() != $cache->getUserId() || $cache->teamcommentAllowed(3));

// cache condition flags
$tpl->assign('cache_needs_maintenance', $cache->getNeedsMaintenance());
$tpl->assign('cache_listing_is_outdated', $cache->getListingOutdatedRelativeToLog($log_id));
$tpl->assign('cache_listing_outdated_log', $cache->getListingOutdatedLogUrl());
$tpl->assign('needs_maintenance', $needsMaintenance);
$tpl->assign('listing_outdated', $listingOutdated);
$tpl->assign('old_listing_outdated', $log_record['listing_outdated']);
$tpl->assign('condition_history', $cache->getConditionHistory());
$tpl->assign('logtype_allows_nm', implode(',', $logtype_allows_nm));

// user data
$tpl->assign('ownerlog', $login->userid == $cache->getUserId());
$tpl->assign('userFound', $user->getStatFound());
$tpl->assign('showstatfounds', $user->showStatFounds());

// recommendation-related data
$ratingParams = $user->getRatingParameters();
$tpl->assign('ratingallowed', $ratingParams['givenRatings'] < $ratingParams['maxRatings']);
$tpl->assign('givenratings', $ratingParams['givenRatings']);
$tpl->assign('maxratings', $ratingParams['maxRatings']);
$tpl->assign('israted', $cache->isRecommendedByUser($user->getUserId()) || isset($_REQUEST['rating']));
$tpl->assign('findsuntilnextrating', $ratingParams['findsUntilNextRating']);
$tpl->assign('isowner', $user->getUserId() == $cache->getUserId());

// password
$tpl->assign('log_pw', $log_pw);

// DNF state
$dnf_by_logger = sql_value(
        "SELECT `type`
         FROM `cache_logs`
         WHERE `cache_id`='&1' AND `user_id`='&2' AND `type` IN (1,2)
         ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC
         LIMIT 1",
        0,
        $cache->getCacheId(),
        $login->userid
    ) == 2;
$tpl->assign('dnf_by_logger', $dnf_by_logger);

// Text / normal HTML / HTML editor
$tpl->assign('use_tinymce', (($descMode == 3) ? 1 : 0));

if ($descMode == 1) {
    $tpl->assign('descMode', 1);
} else {
    if ($descMode == 2) {
        $tpl->assign('descMode', 2);
    } else {
        // TinyMCE
        $tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
        $tpl->add_header_javascript(
            'resource2/tinymce/config/log.js.php?lang=' . strtolower($opt['template']['locale'])
        );
        $tpl->assign('descMode', 3);
    }
}
$tpl->add_header_javascript(editorJsPath());

$tpl->assign('use_log_pw', $use_log_pw);
$tpl->assign('smileypath', $opt['template']['smiley']);
$tpl->assign('smilies', $smiley_a);

$tpl->assign('scrollposx', isset($_REQUEST['scrollposx']) ? $_REQUEST['scrollposx'] + 0 : 0);
$tpl->assign('scrollposy', isset($_REQUEST['scrollposy']) ? $_REQUEST['scrollposy'] + 0 : 0);

// select template mode and send it out
$tpl->assign('editlog', true);

$tpl->acceptsAndPurifiesHtmlInput();
$tpl->display();
