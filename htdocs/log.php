<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Oc\FieldNotes\Entity\FieldNote;
use Oc\GeoCache\Entity\GeocacheLog;

// include libraries
require __DIR__ . '/lib2/web.inc.php';
require_once __DIR__ . '/lib2/logic/cache.class.php';
require_once __DIR__ . '/lib2/logic/user.class.php';
require_once __DIR__ . '/lib2/logic/cachelog.class.php';
require_once __DIR__ . '/lib2/edithelper.inc.php';

// prepare template and menu
$tpl->name = 'log_cache';
$tpl->menuitem = MNU_CACHES_LOG;
$tpl->caching = false;

// check login
$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

// get user object
$user = new user($login->userid);

// get cache_id if not given
$cacheId = 0;
if (isset($_REQUEST['wp'])) {
    $cacheId = cache::cacheIdFromWP($_REQUEST['wp']);
} elseif (isset($_REQUEST['cacheid'])) { // Ocprop
    $cacheId = $_REQUEST['cacheid'];
}

$fieldNote = [];
if (isset($_GET['fieldnoteid']) && !isset($_POST['submitform'])) {
    $rs = sql(
        'SELECT *
         FROM field_note
         WHERE `id` = &1
           AND `user_id` = &2',
        (int) $_GET['fieldnoteid'],
        (int) $user->getUserId()
    );
    $fieldNote = sql_fetch_assoc($rs);
    if (!empty($fieldNote)) {
        $cacheId = $fieldNote['geocache_id'];
    }
}

// check adminstatus of user
$useradmin = ($login->hasAdminPriv() ? 1 : 0);

// prepare array to indicate errors in template
$validate = [];

// log and cache type which can be combined with maintenance state flags
$rs = sql('SELECT `id` FROM `log_types` WHERE `maintenance_logs`');
$logtype_allows_nm = sql_fetch_column($rs);

// proceed loggable, if valid cache_id
if ($cacheId != 0) {
    // get cache object
    $cache = new cache($cacheId);

    // is user cache owner
    $isOwner = ($user->getUserId() == $cache->getUserId());

    // assing ratings to template
    $ratingParams = $user->getRatingParameters();
    $tpl->assign('ratingallowed', $ratingParams['givenRatings'] < $ratingParams['maxRatings']); 
    $tpl->assign('givenratings', $ratingParams['givenRatings']);
    $tpl->assign('maxratings', $ratingParams['maxRatings']);
    $tpl->assign('israted', $cache->isRecommendedByUser($user->getUserId()) || isset($_REQUEST['rating']));
    $tpl->assign('findsuntilnextrating', $ratingParams['findsUntilNextRating']);
    $tpl->assign('isowner', $isOwner);

    // check and prepare form values
    $datesaved = isset($_COOKIE['oclogdate1']) && isset($_COOKIE['oclogdate2']);
    if ($datesaved) {
        $defaultLogYear = substr($_COOKIE['oclogdate1'], 0, 4);
        $defaultLogMonth = substr($_COOKIE['oclogdate1'], 4, 2);
        $defaultLogDay = substr($_COOKIE['oclogdate1'], 6, 2);
    }

    // check if masslog warning is accepted (in cookie)
    $masslogCookieSet = isset($_COOKIE['ocsuppressmasslogwarn']);
    if ($masslogCookieSet) {
        $masslogCookieContent = $_COOKIE['ocsuppressmasslogwarn'] + 0;
    } else {
        // save masslog acception in cookie that expires on midnight if clicked
        if (isset($_REQUEST['suppressMasslogWarning']) && $_REQUEST['suppressMasslogWarning'] == 1) {
            setcookie('ocsuppressmasslogwarn', '1', strtotime('tomorrow'));
        }
    }

    // Ocprop:
    //   logtext, logtype, logday, logmonth, logyear

    $logText = isset($_POST['logtext']) ? trim($_POST['logtext']) : '';
    $logType = isset($_REQUEST['logtype']) ? ($_REQUEST['logtype'] + 0) : null;
    $logDateDay = isset($_POST['logday']) ? trim($_POST['logday']) : ($datesaved ? $defaultLogDay : date('d'));
    $logDateMonth = isset($_POST['logmonth']) ? trim($_POST['logmonth']) : ($datesaved ? $defaultLogMonth : date(
        'm'
    ));
    $logDateYear = isset($_POST['logyear']) ? trim($_POST['logyear']) : ($datesaved ? $defaultLogYear : date('Y'));
    $logTimeHour = isset($_POST['loghour']) ? trim($_POST['loghour']) : '';
    $logTimeMinute = isset($_POST['logminute']) ? trim($_POST['logminute']) : '';
    $needsMaintenance = isset($_POST['needs_maintenance2']) ? $_POST['needs_maintenance2'] + 0 : (isset($_POST['needs_maintenance']) ? $_POST['needs_maintenance'] + 0 : 0);
    $listingOutdated = isset($_POST['listing_outdated2']) ? $_POST['listing_outdated2'] + 0 : (isset($_POST['listing_outdated']) ? $_POST['listing_outdated'] + 0 : 0);
    $confirmListingOk = isset($_POST['confirm_listing_ok']) ? $_POST['confirm_listing_ok'] + 0 : 0;
    $rateOption = isset($_POST['ratingoption']) ? $_POST['ratingoption'] + 0 : 0;
    $rateCache = isset($_POST['rating']) ? $_POST['rating'] + 0 : 0;
    $ocTeamComment = isset($_REQUEST['teamcomment']) ? $_REQUEST['teamcomment'] != 0 : 0;
    $suppressMasslogWarning = isset($_REQUEST['suppressMasslogWarning']) ? $_REQUEST['suppressMasslogWarning'] : ($masslogCookieSet ? $masslogCookieContent : 0);

    if (isset($_GET['fieldnoteid']) && !isset($_POST['submitform']) && !empty($fieldNote)) {
        $_POST['descMode'] = 3;
        $fieldNoteDate = DateTime::createFromFormat('Y-m-d H:i:s', $fieldNote['date']);
        $logDateDay = $fieldNoteDate->format('d');
        $logDateMonth = $fieldNoteDate->format('m');
        $logDateYear = $fieldNoteDate->format('Y');
        $logTimeHour = $fieldNoteDate->format('H');
        $logTimeMinute = $fieldNoteDate->format('i');
        $logText = $fieldNote['text'];
        switch ($fieldNote['type']) {
            case FieldNote::LOG_TYPE_FOUND:
                if (in_array($cache->getType(), \Oc\GeoCache\Entity\Geocache::EVENT_CACHE_TYPES)) {
                    $logType = GeocacheLog::LOG_TYPE_ATTENDED;
                } else {
                    $logType = GeocacheLog::LOG_TYPE_FOUND;
                }
                break;
            case FieldNote::LOG_TYPE_NOT_FOUND:
                $logType = GeocacheLog::LOG_TYPE_NOT_FOUND;
                break;
            case FieldNote::LOG_TYPE_NOTE:
                $logType = GeocacheLog::LOG_TYPE_NOTE;
                break;
            case FieldNote::LOG_TYPE_NEEDS_MAINTENANCE:
                $logType = GeocacheLog::LOG_TYPE_NOTE;
                $needsMaintenance = GeocacheLog::NEEDS_MAINTENANCE_ACTIVATE;
                break;
        }
    }

    if (!in_array($logType, $logtype_allows_nm) || $cache->getType() == 6) {
        $needsMaintenance = $listingOutdated = 0;
    } else {
        if ($needsMaintenance != 1 && $needsMaintenance != 2) {
            $needsMaintenance = 0;
        }
        if ($listingOutdated != 1 && $listingOutdated != 2) {
            $listingOutdated = 0;
        }
    }

    // if not a found log, ignore the rating
    $rateOption = ($logType == 1 || $logType == 7) + 0;

    // get log text editor mode (from form or from userprofile)
    // 1 = text; 2 = HTML; 3 = tinyMCE
    if (isset($_POST['descMode'])) {
        $descMode = $_POST['descMode'] + 0;  // Ocprop: 2
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
        if ($user->getNoHTMLEditor() == 1) {
            $descMode = 1;
        } else {
            $descMode = 3;
        }
        $oldDescMode = $descMode;
    }

    // add javascript-header if editor
    if ($descMode == 3) {
        $tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
        $tpl->add_header_javascript(
            'resource2/tinymce/config/log.js.php?lang=' . strtolower($opt['template']['locale'])
        );
    }
    $tpl->add_header_javascript(editorJsPath());

    // check and prepare log text
    $logText = processEditorInput($oldDescMode, $descMode, $logText, $representLogText);

    // validate date
    $validate['dateOk'] = cachelog::validateDate(
        $logDateYear,
        $logDateMonth,
        $logDateDay,
        $logTimeHour,
        $logTimeMinute,
        isset($_POST['submitform'])
    );

    // Store valid date in temporary cookie; it will be the default for the next log.
    // For a reliable expiration, we need two cookies: One which disappears when the
    // browser is closed, and one which disappears after N hours (for users who
    // keep browsers open ...). See also Redmine #205, #704, #894.

    if ($validate['dateOk']) {
        $cookie_logdate = sprintf('%04d%02d%02d', $logDateYear, $logDateMonth, $logDateDay);
        setcookie('oclogdate1', $cookie_logdate);
        setcookie('oclogdate2', $cookie_logdate, time() + 4 * 60 * 60);
    }

    // check log type
    $validate['logType'] = $cache->logTypeAllowed($logType);

    // check log password
    $log_pw = isset($_POST['log_pw']) ? $_POST['log_pw'] : '';
    $validate['logPw'] = true;
    if (isset($_POST['submitform']) && $cache->requireLogPW()) {
        $validate['logPw'] = $cache->validateLogPW($logType, $log_pw);
    }

    // check listing-ok-confirmation
    if ($listingOutdated == 1 && !$confirmListingOk) {
        $validate['confirmListingOk'] = false;
    }

    // check for errors
    $loggable = array_product($validate);

    // prepare duplicate log error
    $validate['duplicateLog'] = true;

    // all checks done, no error => log
    if (isset($_POST['submitform']) && $loggable) {  // Ocprop
        /*
         * check if time is logged
         * set seconds 00:00:01, means "00:00 was logged"
         * set seconds 00:00:00, means "no time was logged"
         */
        $logTimeSecond = ($logTimeHour . $logTimeMinute != ''
                && $logTimeHour == 0
                && $logTimeMinute == 0) + 0;

        // make time values database ready
        $logDate = date(
            $opt['db']['dateformat'],
            mktime(
                $logTimeHour + 0,
                $logTimeMinute + 0,
                $logTimeSecond,
                $logDateMonth,
                $logDateDay,
                $logDateYear
            )
        );

        // check if duplicate entry already exists (sending form multiple times, or OCProp error)
        if (!cachelog::isDuplicate($cache->getCacheId(), $user->getUserId(), $logType, $logDate, $logText)) {
            // get new cachelog object
            $cacheLog = cachelog::createNewFromCache($cache, $user->getUserId());

            // set values
            $cacheLog->setType($logType);
            $cacheLog->setDate($logDate);
            $cacheLog->setText($logText);
            $cacheLog->setNeedsMaintenance($needsMaintenance);
            $cacheLog->setListingOutdated($listingOutdated);
            $cacheLog->setTextHtml(($descMode != 1) ? 1 : 0);
            $cacheLog->setTextHtmlEdit(($descMode == 3) ? 1 : 0);
            $cacheLog->setOcTeamComment($ocTeamComment);

            // save log values
            $cacheLog->save();

            // update cache status
            $cache->updateCacheStatusFromLatestLog($cacheLog->getLogId(), 0, $logType);

            // update rating (if correct log type, user has ratings to give
            // and is not owner (expect owner adopted cache))
            if ($rateOption && $user->allowRatings()
                && (!$isOwner || ($isOwner && $cache->hasAdopted($user->getUserId())))
            ) {
                if ($rateCache) {
                    $cache->addRecommendation($user->getUserId(), $logDate);
                } else {
                    $cache->removeRecommendation($user->getUserId());
                }
            }

            $cache->save();

            // clear statPic
            $statPic = $user->getStatpic();
            $statPic->deleteFile();

            // delete field note
            if (isset($_REQUEST['fieldnoteid']) && $_REQUEST['fieldnoteid']) {
                sql(
                    'DELETE FROM field_note WHERE `id` = &1 AND `user_id` = &2',
                    (int) $_REQUEST['fieldnoteid'],
                    $user->getUserId()
                );

                $tpl->redirect('/field-notes/');
            }
        }

        // finished, redirect to listing
        $tpl->redirect('viewcache.php?cacheid=' . $cache->getCacheId());
    }

    // assign values to template
    // user info
    $tpl->assign('userFound', $user->getStatFound());
    $tpl->assign('ownerlog', $login->userid == $cache->getUserId());

    // cache infos
    $tpl->assign('cachename', $cache->getName());
    $tpl->assign('cacheid', $cache->getCacheId());
    $tpl->assign('cachetype', $cache->getType());
    $tpl->assign('gcwp', $cache->getWPGC_maintained());

    // date/time
    $tpl->assign('logday', $logDateDay);
    $tpl->assign('logmonth', $logDateMonth);
    $tpl->assign('logyear', $logDateYear);
    $tpl->assign('loghour', $logTimeHour);
    $tpl->assign('logminute', $logTimeMinute);

    // cache condition flags
    $tpl->assign('cache_needs_maintenance', $cache->getNeedsMaintenance());
    $tpl->assign('cache_listing_is_outdated', $cache->getListingOutdated());
    $tpl->assign('cache_listing_outdated_log', $cache->getListingOutdatedLogUrl());
    $tpl->assign('needs_maintenance', $needsMaintenance);
    $tpl->assign('listing_outdated', $listingOutdated);
    $tpl->assign('condition_history', $cache->getConditionHistory());

    // log text
    $tpl->assign('logtext', $representLogText);
    $tpl->assign('descMode', $descMode);

    // logtypes
    $tpl->assign('logtype', $logType);
    $tpl->assign('logtypes', $cache->getUserLogTypes($logType));
    $tpl->assign('typeEditDisabled', false);

    // admin
    $tpl->assign('octeamcommentallowed', $cache->teamcommentAllowed(3));
    $tpl->assign('is_teamcomment', ($ocTeamComment || (!$cache->statusUserLogAllowed() && $useradmin)) ? true : false);
    $tpl->assign('octeamcommentclass', (!$cache->statusUserLogAllowed() && $useradmin) ? 'redtext' : '');
    $tpl->assign('adminAction', $user->getUserId() != $cache->getUserId() || $cache->teamcommentAllowed(3));

    // masslogs
    $tpl->assign('masslogCount', $opt['logic']['masslog']['count']);
    $tpl->assign('masslog', cachelog::isMasslogging($user->getUserId()) && $suppressMasslogWarning == 0);

    // show number of found on log page
    $tpl->assign('showstatfounds', $user->showStatFounds());
    $tpl->assign('use_log_pw', $cache->requireLogPW());

    // smiley list
    $tpl->assign('smilies', $smiley_a);
    $tpl->assign('smileypath', $opt['template']['smiley']);
    $tpl->assign(
        'fieldnoteid', (isset($_REQUEST['fieldnoteid']) && $_REQUEST['fieldnoteid']) ? $_REQUEST['fieldnoteid'] : ''
    );

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
}

// prepare template and display
$tpl->assign('logtype_allows_nm', implode(',', $logtype_allows_nm));
$tpl->assign('scrollposx', isset($_REQUEST['scrollposx']) ? $_REQUEST['scrollposx'] + 0 : 0);
$tpl->assign('scrollposy', isset($_REQUEST['scrollposy']) ? $_REQUEST['scrollposy'] + 0 : 0);
$tpl->assign('validate', $validate);
$tpl->assign('editlog', false);

$tpl->acceptsAndPurifiesHtmlInput();
$tpl->display();
