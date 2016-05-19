<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * log type definitions and functions
 * replaces the obsolete table `cache_logtype`
 *
 * This is included in both, lib1 and lib2 code!
 ****************************************************************************/

// return associative array (id => translated name) of all log types

function get_logtype_names()
{
    global $locale, $opt;

    if (!isset($locale)) {
        $locale = $opt['template']['locale'];
    }

    $log_types = array();
    $rs = sql(
        "SELECT
             `log_types`.`id`,
             IFNULL(`sys_trans_text`.`text`,`log_types`.`en`) AS `type_name`
         FROM `log_types`
         LEFT JOIN `sys_trans_text`
             ON `sys_trans_text`.`trans_id` = `log_types`.`trans_id`
             AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "'"
    );
    while ($r = sql_fetch_array($rs)) {
        $log_types[$r['id']] = $r['type_name'];
    }
    sql_free_result($rs);

    return $log_types;
}


// test if the logged in user is working on a report for a certain cache

function admin_has_open_report($cache_id)
{
    global $login;

    $hasreport = false;

    if ($login->hasAdminPriv(ADMIN_USER)) {
        $rs = sql(
            "SELECT `id` FROM `cache_reports` WHERE `cacheid`='&1' AND `adminid`='&2' AND `status`=2",
            $cache_id,
            $login->userid
        );
        if ($r = sql_fetch_array($rs)) {
            $hasreport = $r['id'] != 0;
        }
        sql_free_result($rs);
    }

    return $hasreport;
}


// returns ordered array of allowed log types for a cache and the active user:
//   type_id => translated type name
// first entry is default for new logs

function get_cache_log_types($cache_id, $old_logtype, $statuslogs = true)
{
    global $login;

    // get input data
    $rs = sql("SELECT `type`, `status`, `user_id` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
    $rCache = sql_fetch_array($rs);
    sql_free_result($rs);

    $cache_type = $rCache['type'];
    $cache_status = $rCache['status'];
    $owner = $login->userid == ($rCache['user_id']);
    $admin_report = admin_has_open_report($cache_id);
    $admin_locked = $login->hasAdminPriv(ADMIN_USER) && in_array($rCache['status'], array(
            6,
            7
        ));

    // build result list
    //
    // Pay attention to okapi/services/logs/submit.php when changing this!

    $allowed_logtypes = array();
    if ($owner || $admin_report || $admin_locked) {
        $allowed_logtypes[] = 3;   // note
    }
    if (($owner && $statuslogs) || $admin_report) {
        if ($cache_status != 5 && (($cache_status != 4 && $cache_status != 7) || $admin_report)) {
            $allowed_logtypes[] = 10;  // ready for search
            $allowed_logtypes[] = 11;  // temporarily not available
            $allowed_logtypes[] = 9;   // archived
            $allowed_logtypes[] = 13;  // locked
        }
        if ($admin_report || $old_logtype == 14) {
            $allowed_logtypes[] = 14;
        }  // locked, invisible
    }
    if ($cache_type == 6) { // event
        $allowed_logtypes[] = 8;   // will attend
        $allowed_logtypes[] = 7;   // attended
    } else {
        $allowed_logtypes[] = 1;   // found
        $allowed_logtypes[] = 2;   // not found
    }
    if (!($owner || $admin_report || $admin_locked)) {
        $allowed_logtypes[] = 3;   // note
    }

    // always allow to keep the existing logtype when the log is edited by an admin
    if ($login->hasAdminPriv(ADMIN_USER) &&
        $old_logtype > 0 && !in_array($old_logtype + 0, $allowed_logtypes)
    ) {
        $allowed_logtypes[] = $old_logtype;
    }

    return $allowed_logtypes;
}


function logtype_ok($cache_id, $logtype_id, $old_logtype)
{
    return in_array($logtype_id, get_cache_log_types($cache_id, $old_logtype));
}


function teamcomment_allowed($cache_id, $logtype_id, $old_teamcomment = false)
{
    global $login, $opt;

    if (!$login->hasAdminPriv(ADMIN_USER)) {
        return false;
    } elseif ($logtype_id != 3 && ($logtype_id < 9 || $logtype_id > 14)) {
        return false;
    } elseif ($old_teamcomment) {
        return true;
    } else {
        $rs = sql("SELECT `user_id`,`status` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
        if ($rCache = sql_fetch_array($rs)) {
            if ($login->userid == $rCache['user_id']) {
                // not allowed for own caches
                $allowed = false;
            } elseif (!$opt['logic']['admin']['team_comments_only_for_reports'] || admin_has_open_report($cache_id)) {
                // allowed for report processing by admins
                $allowed = true;
            } elseif ($login->hasAdminPriv(ADMIN_USER) && in_array($rCache['status'], [6,7])) {
                // allowed for admins && locked caches, see http://forum.opencaching.de/index.php?topic=3102.msg39517#msg39517
                $allowed = true;
            } else {
                $allowed = false;
            }
        } else {
            $allowed = false;
        }
        sql_free_result($rs);

        return $allowed;
    }
}
