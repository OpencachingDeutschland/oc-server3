<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 * TODO:
 *        - limit / clean up archive ?
 *        - do not archive anything with is not from our node
 ***************************************************************************/

/*
    The following tables are monitored. On changes the OLD data is recorded
    (except for cache_coordinates and cache_countries, where the NEW data is recorded
    for historical reasons; I = on insert, U = on update, D = on delete;
    further explanations below:
                                                                recording    limited #  max.
    data table         archive tables        ops    recorded by   date         per cache  one p.d.
    ----------         --------------        ---    -----------   ----------   --------   --------
- caches             cache_coordinates     IU     trigger       datetime     yes        no
-                    cache_countries       IU     trigger       datetime     yes        no
c                    caches_modified        U     trigger       date         yes        yes
c cache_attributes   cache_attr._modified  I D    trigger       date         yes        yes
c cache_desc         cache_desc_modified   IUD    trigger       date         yes        yes
x cache_logs         cache_logs_archived     D    removelog     datetime     no         no
x                    cache_logs_restored   I*     here          datetime     no         no
  pictures           pictures_modified     IUD    trigger       datetime     no         no

    * only insertions on restore

    (Additional waypoints in table 'coordinates' are currently not monitored. Archiving
    and restoring waypoints would be as complex as log entries and is not important at
    vandalism-restore.)

    The whole mechanism heavily relies on autoincrement IDs being unique on one system
    and NEVER be reused after record deletion. DON'T EVEN THINK ABOUT TRUNCATING AND
    RENUMBERING ANY OF THE ABOVE 'DATA TABLES' UNDER ANY CIRCUMSTANCES! Use bigint IDs
    if you worry about how to store your 3 billion logs.

    Special fields used for recovery are:
        - date_modified in all tables except for logs.deletion_date and coords/countries.date_created:
            the date when the change took place and was recorded
        - deleted_by in cache_logs_archived: thee user_id who deleted the data
            (to determine if it can be vandalism by cache owner);
        - restored_by in all tables except for cache_logs_*: the restoring admin's user id (or 0)
        - operation in pictures_modified: I/U/D for a recorded insert/delete/update
        - was_set in attributes_modified: 1 if the attribute was set before the change
        - original_id in cache_logs_restored and pictures_modified: the original id of restored
            objects, needed to maintain archive integrity

    Insertion of a new record is flagged by:
        - caches_attributes_modified:  was_set = 0
        - cache_desc_modified:         desc = null
        - pictures_modified:           operation = 'I'
        - cache_logs_restored:         presence of data record (only for restored logs)

    uuids are not recorded (except for cache_logs_archived, which is used elsewhere).
    Restored records are just copies of the old records, not the old records themselves,
    so they receive new uuids!
    The old records stay untouched and deleted in the archives for further reference!

    To save log space, the following is NOT recorded:
        - changes on any data which
              - need not to be restored (e.g. cache status),
                - is recreated on restore (e.g. uuids) or
                - can be restored by other means (e.g. statistics, thumbnails)
        - additional changes on the same dataset on the same day for caches_modified,
            cache_desc_modified and cache_attributes_modified. They are blocked by unique
            indexes which include the date_modified (no time there!).
        - operations on the same day when the cache was created
        - operations on own logs, including pictures: own logs are excempt from vandalism-restore
        - cache attribs, desc and picture changes on the same day when the record was created

  On coordinates and countries every change is recorded for XML interface reasons.
  On logs and pictures, every change must be recorded, because recognizing multiple restores
    of the same record would not be feasible.

    On or after restore, the following is automatically fixed:
        - caches.desc_languages und .default_desclang
                             - by cache_desc triggers
        - cache_location     - by high-frequency cache_location cronjob (per last_modified)
        - cache_npa_areas    - by cache_npa_areas cronjob (per modify-trigger -> need_npa_recalc)
        - stat_caches        - by cache_logs triggers
        - stat_cache_logs    - by cache_logs triggers
        - cache_logs.picture - by picture triggers
        - pic thumbnails     - by thumbs.php (per thumb_last_generated default value)

    There is no special treatment of changes by restore operations, so they are recorded
    and revertible, too, if not done on the same day as the vandalism.

    The table listing_restored keeps track of the admins who and when restored listings.

    Code updates:
        When adding fields to caches, cache_attributes, cache_desc, cache_logs or pictures
        which are not filled automatically by triggers or cronjobs, those fields must be added
        to the archive-and-restore mechanism, i.e.

            - to the corresponding lib2/logic classes (currently used only for logs and pics,
                but anyway ...)
            - to maintain.php (archiving triggers; use INSERT IGNORE for all recording)
            - to restorecaches.php (functions get_archive_data and restore_caches),
            - to the *_modified tables and
            - eventually to restorecaches.tpl (step 4).

        While the restore mechanism itself is robust against database extensions and will not
        crash when fields are added, the archives would grow incomplete and so would be the
        restored listings. Also, CC-ND license at OC.de requires listings to be published
        unchanged, so all-or-nothing-restore per listing is desireable.

        When adding new TABLES for new listing-related data which can be vandalized, decide if -
        preferrably - max. one recorded change per day is (a) sufficient or (b) not. It is
        sufficient if the number of records inserted for one cache is limited by some property
        (like attribute types oder languages). It is insufficient if there can be an arbitrary
        number of records (like logs and pictures).
        for (a)
            - define date_modified field as 'date'
            - define an unique index on parent id, date_modified and the limiting property/ies
                  (e.g. see caches_attributes_modified 'cache_id' index)
            - use INSERT ... ON DUPLICATE UPDATE ...  on restore
        for (b)
            - define date_modified field as 'datetime'
            - truncate date_modified in get_archive_data() to the date, i.e. left 10 chars
            - order ascending by (full) date_modified in get_archive_data()
            - define an 'original_id' field and implement a mechanism like get_current_picid()
                  to take care of changing ids and original_ids
            - use the corresponding parent table's mechanism for parent ids when restoring
                  deleted records, if the parent table is also an arbitrary-number table
                     (see call to get_current_logid() when restoring deleted pictures)
            - for index definitions see e.g. cache_logs_restored

        If you can't handle these requirements, don't add the new fields/tables.

*/

require __DIR__ . '/lib2/web.inc.php';
require_once __DIR__ . '/lib2/logic/labels.inc.php';

$tpl->name = 'restorecaches';
$tpl->menuitem = MNU_ADMIN_RESTORE;
$tpl->assign('error', '');
$tpl->assign('step', 0);

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

if (!$login->hasAdminPriv(ADMIN_RESTORE)) {
    $tpl->error(ERROR_NO_ACCESS);
}

// params
if (isset($_REQUEST['finduser']) && isset($_REQUEST['username'])) {
    // STEP 2: verify username

    $tpl->assign('step', 1);
    $tpl->assign('username', $_REQUEST['username']);
    $rs = sql(
        "SELECT `user_id`, `username`, `is_active_flag` FROM `user` WHERE `username`='&1'",
        $_REQUEST['username']
    );
    $r = sql_fetch_assoc($rs);
    sql_free_result($rs);
    if ($r == false) {
        $tpl->assign('error', 'userunknown');
        $tpl->display();
    }
    $tpl->assign('username', $r['username']);

    // get cache set for this user
    $user_id = $r['user_id'];
    $rs = sql(
        "SELECT
            `cache_id`,
            `wp_oc`,
            `name`,
            `latitude`,
            `longitude`,
            `status`,
            LEFT(`listing_last_modified`,10) AS `last_modified`,
            (SELECT COUNT(*) FROM `cache_logs` WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`) AS `logs`
        FROM `caches`
        WHERE `user_id`='&1'
        AND `status`!=5",
        $user_id
    );
    $caches = array();
    while ($rCache = sql_fetch_assoc($rs)) {
        $coord = new coordinate($rCache['latitude'], $rCache['longitude']);
        $rCache['coordinates'] = $coord->getDecimalMinutes();
        $rCache['data'] = get_archive_data(array($rCache['cache_id']));
        if (count($rCache['data'])) {
            $keys = array_keys($rCache['data']);
            $rCache['date'] = $keys[0];
        }
        $caches[] = $rCache;
    }
    sql_free_result($rs);

    if (count($caches) == 0) {
        $tpl->assign('error', 'nocaches');
    } else {
        // STEP 3: select caches to restore
        $tpl->assign('step', 3);
        $tpl->assign('aCaches', $caches);
        $tpl->assign('disabled', $r['is_active_flag'] == 0);
    }
} elseif (isset($_REQUEST['username']) && isset($_REQUEST['caches'])) {
    // STEP 4: select date

    $tpl->assign('step', 4);
    $tpl->assign('username', $_REQUEST['username']);
    $tpl->assign(
        'disabled',
        sql_value(
            "SELECT NOT `is_active_flag` FROM `user` WHERE `username`='&1'",
            0,
            $_REQUEST['username']
        )
    );

    $cacheids = array();
    foreach ($_REQUEST as $param => $value) {
        if (substr($param, 0, 6) == 'cache_') {
            $cacheids[] = substr($param, 6);
        }
    }

    if (count($cacheids) == 0) {
        $tpl->assign('error', 'nocaches');
        $tpl->display();
    }

    $dates = get_archive_data($cacheids);
    if (count($dates) == 0) {
        $tpl->assign('error', 'nodata');
        $tpl->display();
    }

    $today = sql_value("SELECT LEFT(NOW(),10)", 0);
    $today_usermod = false;
    if (isset($dates[$today])) {
        foreach ($dates[$today] as $cache_changed) {
            if (strpos($cache_changed, "userchange")) {
                $today_usermod = true;
            }
        }
    }

    $tpl->assign('cachelist', urlencode(implode(',', $cacheids)));
    $tpl->assign('dates', $dates);
    $tpl->assign('today', $today_usermod);
    $tpl->assign('rootadmin', $opt['page']['develsystem'] && $login->hasAdminPriv(ADMIN_ROOT));
    $tpl->display();
} elseif (isset($_REQUEST['username']) && ($_REQUEST['cacheids']) && isset($_REQUEST['doit'])) {
    // STEP 5: restore data

    $tpl->assign('step', 5);
    $tpl->assign('username', $_REQUEST['username']);
    $tpl->assign(
        'disabled',
        sql_value(
            "SELECT NOT `is_active_flag` FROM `user` WHERE `username`='&1'",
            0,
            $_REQUEST['username']
        )
    );

    $simulate = isset($_REQUEST['simulate']) && $_REQUEST['simulate'];
    $tpl->assign('simulate', $simulate);

    $restore_date = isset($_REQUEST['dateselect']) ? $_REQUEST['dateselect'] : "";
    $restore_options = array();
    foreach ($_REQUEST as $param => $value) {
        if (substr($param, 0, 8) == "restore_") {
            $restore_options[] = substr($param, 8);
        }
    }

    if ($restore_date == "") {
        $tpl->assign('error', 'nodate');
        $tpl->display();
    } elseif (count($restore_options) == 0) {
        $tpl->assign('error', 'nochecks');
        $tpl->display();
    }
    if ((!isset($_REQUEST['sure']) || !$_REQUEST['sure']) && !$simulate) {
        $tpl->assign('error', 'notsure');
        $tpl->display();
    }

    $cacheids = explode(",", urldecode($_REQUEST['cacheids']));
    $tpl->assign(
        'restored',
        restore_listings($cacheids, $restore_date, $restore_options, $simulate)
    );
    $tpl->assign('date', $restore_date);
} else {
    // STEP 1: ask for username

    $tpl->assign('step', 1);
}

$tpl->display();


// get readable list of recorded data changes for a list of cache(id)s

function get_archive_data($caches)
{
    $cachelist = "(" . implode(",", $caches) . ")";
    $data = array();
    $admins = array();

    // make waypoint index
    $rs = sql("SELECT `cache_id`, `wp_oc` FROM `caches` WHERE `cache_id` IN " . $cachelist);
    while ($r = sql_fetch_assoc($rs)) {
        $wp_oc[$r['cache_id']] = $r['wp_oc'];
    }
    sql_free_result($rs);

    // cache coordinates
    $rs = sql(
        "SELECT
            `cache_id`,
            LEFT(`date_created`,10) AS `date_modified`,
            `longitude`,
            `latitude`,
            `restored_by`
        FROM `cache_coordinates`
        WHERE `cache_id` IN " . $cachelist . "
        ORDER BY `date_created` ASC"
    );
    // order is relevant, because multiple changes per day possible
    $lastcoord = array();
    while ($r = sql_fetch_assoc($rs)) {
        $coord = new coordinate($r['latitude'], $r['longitude']);
        $coord = $coord->getDecimalMinutes();
        $coord = $coord['lat'] . " " . $coord['lon'];
        if (isset($lastcoord[$r['cache_id']]) && $coord != $lastcoord[$r['cache_id']]) {
            // the database contains lots of old coord records with unchanged coords, wtf?
            append_data($data, $admins, $wp_oc, $r, "coord", $lastcoord[$r['cache_id']], $coord);
        }
        $lastcoord[$r['cache_id']] = $coord;
    }
    sql_free_result($rs);

    // cache country
    $rs = sql("SELECT `cache_id`, LEFT(`date_created`,10) AS `date_modified`, `country`, `restored_by`
               FROM `cache_countries`
                         WHERE `cache_id` IN " . $cachelist . "
                         ORDER BY `date_created` ASC");
    // order is relevant, because multiple changes per day possible
    $lastcountry = array();
    while ($r = sql_fetch_assoc($rs)) {
        if (isset($lastcountry[$r['cache_id']]) && $r['country'] != $lastcountry[$r['cache_id']]) {
            // the database contains some old country records with unchanged coords, wtf?
            append_data($data, $admins, $wp_oc, $r, "country", $lastcountry[$r['cache_id']], $r['country']);
        }
        $lastcountry[$r['cache_id']] = $r['country'];
    }
    sql_free_result($rs);

    // all other cache data
    // first the current data ...
    $nextcd = array();
    $rs = sql("SELECT * FROM `caches` WHERE `cache_id` IN " . $cachelist);
    while ($r = sql_fetch_assoc($rs)) {
        $nextcd[$r['wp_oc']] = $r;
        $user_id = $r['user_id'];     // is used later for logs
    }
    sql_free_result($rs);

    // .. and then the changes
    $rs = sql(
        "SELECT * FROM `caches_modified`
        WHERE `cache_id` IN " . $cachelist . "
        ORDER BY `date_modified` DESC"
    );
    while ($r = sql_fetch_assoc($rs)) {
        $wp = $wp_oc[$r['cache_id']];
        if ($r['name'] != $nextcd[$wp]['name']) {
            append_data($data, $admins, $wp_oc, $r, "name", $r['name'], $nextcd[$wp]['name']);
        }
        if ($r['type'] != $nextcd[$wp]['type']) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "type",
                labels::getLabelValue('cache_type', $r['type']),
                labels::getLabelValue('cache_type', $nextcd[$wp]['type'])
            );
        }
        if ($r['size'] != $nextcd[$wp]['size']) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "size",
                labels::getLabelValue('cache_size', $r['size']),
                labels::getLabelValue('cache_size', $nextcd[$wp]['size'])
            );
        }
        if ($r['difficulty'] != $nextcd[$wp]['difficulty']) {
            append_data($data, $admins, $wp_oc, $r, "D", $r['difficulty'] / 2, $nextcd[$wp]['difficulty'] / 2);
        }
        if ($r['terrain'] != $nextcd[$wp]['terrain']) {
            append_data($data, $admins, $wp_oc, $r, "T", $r['terrain'] / 2, $nextcd[$wp]['terrain'] / 2);
        }
        if ($r['search_time'] != $nextcd[$wp]['search_time']) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "time",
                $r['search_time'] . '&nbsp;h',
                $nextcd[$wp]['search_time'] . '&nbsp;h'
            );
        }
        if ($r['way_length'] != $nextcd[$wp]['way_length']) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "way",
                $r['way_length'] . '&nbsp;km',
                $nextcd[$wp]['way_length'] . '&nbsp;km'
            );
        }
        if ($r['wp_gc'] != $nextcd[$wp]['wp_gc']) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "GC ",
                format_wp($r['wp_gc']),
                format_wp($nextcd[$wp]['wp_gc'])
            );
        }
        if ($r['wp_nc'] != $nextcd[$wp]['wp_nc']) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "GC ",
                format_wp($r['wp_nc']),
                format_wp($nextcd[$wp]['wp_nc'])
            );
        }
        if ($r['date_hidden'] != $nextcd[$wp]['date_hidden']) {
            append_data($data, $admins, $wp_oc, $r, "hidden", $r['date_hidden'], $nextcd[$wp]['date_hidden']);
        }

        $nextcd[$wp] = $r;
    }
    sql_free_result($rs);

    // attributes
    $rs = sql(
        "SELECT * FROM `caches_attributes_modified`
         WHERE `cache_id` IN " . $cachelist . "  /* OConly attrib is shown, but not restorable */
         ORDER BY `date_modified` ASC"
    );   // order doesn't matter as long it is date only
    while ($r = sql_fetch_assoc($rs)) {
        append_data(
            $data,
            $admins,
            $wp_oc,
            $r,
            "attrib",
            ($r['was_set'] ? "-" : "+") . labels::getLabelValue('cache_attrib', $r['attrib_id']),
            ''
        );
    }
    sql_free_result($rs);

    // descriptions
    // first the current data ...
    $nextdesc = array();
    $rs = sql(
        "SELECT
            `cache_id`,
            `language`,
            LENGTH(`desc`) AS `dl`,
            LENGTH(`hint`) AS `hl`,
            LENGTH(`short_desc`) AS `sdl`
        FROM `cache_desc`
        WHERE `cache_id` IN " . $cachelist
    );
    while ($r = sql_fetch_assoc($rs)) {
        if (!isset($nextdesc[$r['cache_id']])) {
            $nextdesc[$r['cache_id']] = [];
        }
        $nextdesc[$r['cache_id']][$r['language']] = $r;
    }
    sql_free_result($rs);

    // ... and then the changes
    $rs = sql(
        "SELECT
            `cache_id`,
            `date_modified`,
            `language`,
            LENGTH(`desc`) AS `dl`,
            LENGTH(`hint`) AS `hl`,
            LENGTH(`short_desc`) AS `sdl`,
            `restored_by`
        FROM `cache_desc_modified`
        WHERE `cache_id` IN " . $cachelist . "
        ORDER BY `date_modified` DESC"
    );
    // order doesn't matter as long only one change per day is recorded
    while ($r = sql_fetch_assoc($rs)) {
        $wp = $wp_oc[$r['cache_id']];
        if (!isset($nextdesc[$r['cache_id']]) || !isset($nextdesc[$r['cache_id']][$r['language']])) {
            $next = [
                'dl' => 0,
                'hl' => 0,
                'sdl' => 0
            ];
        } else {
            $next = $nextdesc[$r['cache_id']][$r['language']];
        }

        if ($r['dl'] + 0 != $next['dl'] + 0) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "desc(" . $r['language'] . ")",
                $r['dl'] + 0,
                ($next['dl'] + 0) . ' bytes'
            );
        }
        if ($r['hl'] + 0 != $next['hl'] + 0) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "hint(" . $r['language'] . ")",
                $r['hl'] + 0,
                ($next['hl'] + 0) . ' bytes'
            );
        }
        if ($r['sdl'] + 0 != $next['sdl'] + 0) {
            append_data(
                $data,
                $admins,
                $wp_oc,
                $r,
                "shortdesc(" . $r['language'] . ")",
                $r['sdl'] + 0,
                ($next['sdl'] + 0) . ' bytes'
            );
        }

        $nextdesc[$r['cache_id']][$r['language']] = $r;
    }
    sql_free_result($rs);

    // logs
    $rs = sql(
        "SELECT
            `op`,
            LEFT(`date_modified`,10) AS `date_modified`,
            `cache_id`,
            `logs`.`user_id`,
            `type`,
            `date`,
            `restored_by`,
            `username`
        FROM
              (SELECT 1 AS `op`, `deletion_date` AS `date_modified`, `cache_id`,
                    `user_id`, `type`, `date`, `restored_by`
                   FROM `cache_logs_archived`
                  WHERE `cache_id` IN " . $cachelist . "AND `deleted_by`='&1' AND `user_id`<>'&1'
                  UNION
                  SELECT 2 AS `op`, `date_modified`, `cache_id`,
                       (SELECT `user_id` FROM `cache_logs_archived` WHERE `id`=`original_id`),
                       (SELECT `type` FROM `cache_logs_archived` WHERE `id`=`original_id`),
                       (SELECT `date` FROM `cache_logs_archived` WHERE `id`=`original_id`),
                       `restored_by`
                 FROM `cache_logs_restored`
                  WHERE `cache_id` IN " . $cachelist . ") `logs`
                INNER JOIN `user` ON `user`.`user_id`=`logs`.`user_id`
              ORDER BY `logs`.`date_modified` ASC",
        // order may not be exact when redoing reverts, because delete and insert
        // operations then are so quick that dates in both tables are the same
        $user_id
    );
    while ($r = sql_fetch_assoc($rs)) {
        append_data(
            $data,
            $admins,
            $wp_oc,
            $r,
            $r["op"] == 1 ? "dellog" : "restorelog",
            "<a href='viewprofile.php?userid=" . $r['user_id'] . "' target='_blank'>" . $r['username'] . "</a>/" . $r['date'],
            ''
        );
    }
    sql_free_result($rs);

    // pictures

    /* For sake of simplification, we
     *   - have stored the name of inserted pictures in pictures_modified
     *   - give no detailed information on picture property changes. This will be very
     *       rare in case of vandalism ...
     */

    $piccacheid = "IF(`object_type`=2, `object_id`, IF(`object_type`=1, IFNULL((SELECT `cache_id` FROM `cache_logs` WHERE `id`=`object_id`),(SELECT `cache_id` FROM `cache_logs_archived` WHERE `id`=`object_id`)), 0))";
    $rs = sql(
        "SELECT *, " . $piccacheid . "AS `cache_id` FROM `pictures_modified`
         WHERE " . $piccacheid . " IN " . $cachelist . "
         ORDER BY `date_modified` ASC"
    );  // order is relevant for the case of restore-reverts
    while ($r = sql_fetch_assoc($rs)) {
        $r['date_modified'] = substr($r['date_modified'], 0, 10);
        switch ($r['operation']) {
            case 'I':
                $picchange = "add";
                break;
            case 'U':
                $picchange = "mod";
                break;
            case 'D':
                $picchange = "del";
                break;
        }
        switch ($r['object_type']) {
            case 1:
                $picchange .= "-log";
                break;
            case 2:
                $picchange .= "-cache";
                break;
        }
        append_data($data, $admins, $wp_oc, $r, $picchange . "pic", $r['title'], '');
    }
    sql_free_result($rs);

    // admins
    foreach ($admins as $adate => $adata) {
        foreach ($adata as $awp => $alist) {
            $data[$adate][$awp] .= "<br /><strong class='adminrestore'>admins:</strong> " . implode(',', $alist);
        }
    }

    // done
    ksort($data);

    return array_reverse($data, true);
}


function format_wp($wp)
{
    if ($wp == "") {
        return "(leer)";
    } else {
        return $wp;
    }
}


/**
 * @param $data
 * @param $admins
 * @param $wp_oc
 * @param $r
 * @param $field
 * @param $oldvalue
 * @param $newvalue
 */
function append_data(&$data, &$admins, $wp_oc, $r, $field, $oldvalue, $newvalue)
{
    if (!isset($r['date_modified'])) {
        die("internal error: date_modified not set for $field");
    }
    $mdate = $r['date_modified'];
    $wp = $wp_oc[$r['cache_id']];
    $byadmin = ($r['restored_by'] > 0);

    if (!isset($data[$mdate])) {
        $data[$mdate] = [];
    }

    $text = "<strong";
    if ($byadmin) {
        $text .= " class='adminrestore'";
    } else {
        $text .= " class='userchange'";
    }
    $text .= ">$field</strong>: $oldvalue" . ($newvalue != "" ? " &rarr; $newvalue" : "");
    if (isset($data[$mdate][$wp])) {
        $data[$mdate][$wp] .= ", " . $text;
    } else {
        $data[$mdate][$wp] = $text;
    }

    if ($byadmin) {
        if (!isset($admins[$mdate])) {
            $admins[$mdate] = [];
        }
        if (!isset($admins[$mdate][$wp])) {
            $admins[$mdate][$wp] = [];
        }
        $admins[$mdate][$wp][$r['restored_by'] + 0]
            = "<a href='viewprofile.php?userid=" . $r['restored_by'] . "' target='_blank'>" .
            sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", "", $r['restored_by']) .
            "</a>";
    }
}


function restore_listings($cacheids, $rdate, $roptions, $simulate)
{
    global $opt, $login;

    sql("SET @restoredby='&1'", $login->userid);         // is evaluated by trigger functions
    sql_slave("SET @restoredby='&1'", $login->userid);

    $restored = array();

    foreach ($cacheids as $cacheid) {
        $modified = false;

        // get current cache data
        $rs = sql("SELECT * FROM `caches` WHERE `cache_id`='&1'", $cacheid);
        $cache = sql_fetch_assoc($rs);
        sql_free_result($rs);
        $wp = $cache['wp_oc'];
        $user_id = $cache['user_id'];

        // coordinates
        if (in_array("coords", $roptions) &&
            sql_value(
                "SELECT `cache_id` FROM `cache_coordinates`
                WHERE `cache_id`='&1' AND `date_created`>='&2'",
                0,
                $cacheid,
                $rdate
            )
        ) {
            $rs = sql(
                "SELECT `latitude`, `longitude` FROM `cache_coordinates`
                WHERE `cache_id`='&1' AND `date_created` < '&2'
                ORDER BY `date_created` DESC
                LIMIT 1",
                $cacheid,
                $rdate
            );
            if ($r = sql_fetch_assoc($rs)) { // should always be true ...
                if (!$simulate) {
                    sql(
                        "UPDATE `caches` SET `latitude`='&1', `longitude`='&2' WHERE `cache_id`='&3'",
                        $r['latitude'],
                        $r['longitude'],
                        $cacheid
                    );
                }

                $restored[$wp]['coords'] = true;
            }
            sql_free_result($rs);
        }

        // country
        if (in_array("coords", $roptions) &&
            sql_value(
                "SELECT `cache_id` FROM `cache_countries`
                WHERE `cache_id`='&1' AND `date_created`>='&2'",
                0,
                $cacheid,
                $rdate
            )
        ) {
            $rs = sql(
                "SELECT `country` FROM `cache_countries`
                WHERE `cache_id`='&1' AND `date_created` < '&2'
                ORDER BY `date_created` DESC
                LIMIT 1",
                $cacheid,
                $rdate
            );
            if ($r = sql_fetch_assoc($rs)) { // should always be true ...
                if (!$simulate) {
                    sql(
                        "UPDATE `caches` SET `country`='&1'  WHERE `cache_id`='&2'",
                        $r['country'],
                        $cacheid
                    );
                }

                $restored[$wp]['country'] = true;
            }
            sql_free_result($rs);
        }

        // other cache data
        $rs = sql(
            "SELECT * FROM `caches_modified`
            WHERE `cache_id`='&1' AND `date_modified` >='&2'
            ORDER BY `date_modified` ASC
            LIMIT 1",
            $cacheid,
            $rdate
        );

        $fields = [
            'name' => 'settings',
            'type' => 'settings',
            'size' => 'settings',
            'date_hidden' => 'settings',
            'difficulty' => 'settings',
            'terrain' => 'settings',
            'search_time' => 'settings',
            'way_length' => 'settings',
            'wp_gc' => 'waypoints',
            'wp_nc' => 'waypoints'
        ];

        if ($r = sql_fetch_assoc($rs)) {// can be false
            $setfields = "";
            foreach ($fields as $field => $ropt) {
                if (in_array($ropt, $roptions) && $r[$field] != $cache[$field]) {
                    if ($setfields != "") {
                        $setfields .= ",";
                    }
                    $setfields .= "`$field`='" . sql_escape($r[$field]) . "'";
                    $restored[$wp][$field] = true;
                }
            }
            if ($setfields != "" && !$simulate) {
                sql("UPDATE `caches` SET " . $setfields . " WHERE `cache_id`='&1'", $cacheid);
            }
        }
        sql_free_result($rs);

        // attributes
        if (in_array('settings', $roptions)) {
            $rs = sql(
                "SELECT * FROM `caches_attributes_modified`
                WHERE `cache_id`='&1' AND `date_modified`>='&2' AND `attrib_id` != 6 /* OConly */
                ORDER BY `date_modified` DESC",
                $cacheid,
                $rdate
            );

            // revert all attribute changes in reverse order.
            // recording limit of one change per attribute, cache and day ensures that no exponentially
            // growing list of recording entries can emerge from multiple reverts.

            while ($r = sql_fetch_assoc($rs)) {
                if (!$simulate) {
                    if ($r['was_set']) {
                        sql(
                            "INSERT IGNORE INTO `caches_attributes` (`cache_id`,`attrib_id`)
                            VALUES ('&1','&2')",
                            $cacheid,
                            $r['attrib_id']
                        );
                    } else {
                        sql(
                            "DELETE FROM `caches_attributes` WHERE `cache_id`='&1' AND `attrib_id`='&2'",
                            $cacheid,
                            $r['attrib_id']
                        );
                    }
                }
                $restored[$wp]['attributes'] = true;
            }
            sql_free_result($rs);
        }

        // descriptions
        if (in_array('desc', $roptions)) {
            $rs = sql(
                "SELECT * FROM `cache_desc_modified`
                WHERE `cache_id`='&1' AND `date_modified`>='&2'
                ORDER BY `date_modified` DESC",
                $cacheid,
                $rdate
            );

            // revert all desc changes in reverse order.
            // recording limit of one change per language, cache and day ensures that no exponentially
            // growing list of recording entries can emerge from restore-reverts.

            while ($r = sql_fetch_assoc($rs)) {
                if (!$simulate) {
                    if ($r['desc'] === null) { // was newly created -> delete
                        sql(
                            "DELETE FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'",
                            $cacheid,
                            $r['language']
                        );
                    } else {// id, uuid, date_created and last_modified are set automatically
                        sql(
                            "INSERT INTO `cache_desc`
                            (`node`, `cache_id`, `language`, `desc`, `desc_html`, `desc_htmledit`, `hint`, `short_desc`)
                            VALUES ('&1','&2','&3','&4','&5','&6','&7','&8')
                            ON DUPLICATE KEY UPDATE
                            `desc`='&4', `desc_html`='&5', `desc_htmledit`='&6', `hint`='&7', `short_desc`='&8'",
                            $opt['logic']['node']['id'],
                            $cacheid,
                            $r['language'],
                            $r['desc'],
                            $r['desc_html'],
                            $r['desc_htmledit'],
                            $r['hint'],
                            $r['short_desc']
                        );
                    }
                }

                $restored[$wp]['description(s)'] = true;
            }
            sql_free_result($rs);
        }

        // logs
        // ... before pictures, so that restored logpics have a parent
        if (in_array('logs', $roptions)) {
            $rs = sql(
                "
                SELECT * FROM (
                    SELECT
                        `id`,
                        -1 AS `node`,
                        `date_modified`,
                        `cache_id`,
                        0 AS `user_id`,
                        0 AS `type`,
                        '0' AS `oc_team_comment`,
                        '0' AS `date`,
                        '' AS `text`,
                        0 AS `text_html`,
                        0 AS `text_htmledit`,
                        0 AS `needs_maintenance`,
                        0 AS `listing_outdated`,
                        `original_id`
                    FROM `cache_logs_restored`
                    WHERE `cache_id`='&1' AND `date_modified` >= '&2'
                    UNION
                    SELECT
                        `id`,
                        `node`,
                        `deletion_date`,
                        `cache_id`,
                        `user_id`,
                        `type`,
                        `oc_team_comment`,
                        `date`,
                        `text`,
                        `text_html`,
                        `text_htmledit`,
                        `needs_maintenance`,
                        `listing_outdated`,
                        0 AS `original_id`
                    FROM `cache_logs_archived`
                    WHERE
                        `cache_id`='&1'
                        AND `deletion_date` >= '&2'
                        AND `deleted_by`='&3'
                        AND `user_id` != '&3'
                ) `logs`
                ORDER BY `date_modified` ASC",
                $cacheid,
                $rdate,
                $user_id
            );

            // We start with the oldest entry and will touch each log ony once:
            // After restoring its state, it is added to $logs_processed (by its last known id),
            // and all further operations on the same log are ignored. This prevents unnecessary
            // operations and flooding pictures_modified on restore-reverts.
            $logs_processed = array();

            while ($r = sql_fetch_assoc($rs)) {
                $error = "";
                $logs_restored = false;

                // the log's id may have changed by multiple delete-and-restores
                $revert_logid = get_current_logid($r['id']);
                if (!in_array($revert_logid, $logs_processed)) {
                    if ($r['node'] == - 1) {
                        // if it was not already deleted by a later restore operation ...
                        if (sql_value("SELECT `id` FROM `cache_logs` WHERE `id`='&1'", 0, $revert_logid) != 0) {
                            if (!$simulate) {
                                sql(
                                    "INSERT INTO `cache_logs_archived`
                                    SELECT *, '0', '&2', '&3' FROM `cache_logs` WHERE `id`='&1'",
                                    $revert_logid,
                                    $user_id, // original deletor's ID and not restoring admin's ID!
                                    $login->userid
                                );
                                sql("DELETE FROM `cache_logs` WHERE `id`='&1'", $revert_logid);
                                // This triggers an okapi_syncbase update, if OKAPI is installed:
                                sql(
                                    "UPDATE `cache_logs_archived` SET `deletion_date`=NOW() WHERE `id`='&1'",
                                    $revert_logid
                                );
                            }
                            $logs_restored = true;
                        }
                       // if it was not already restored by a later restore operation ...
                    } elseif (sql_value("SELECT `id` FROM `cache_logs` WHERE `id`='&1'", 0, $revert_logid) == 0) {
                        // id, uuid, date_created and last_modified are set automatically;
                        // picture will be updated automatically on picture-restore
                        $log = new cachelog();
                        $log->setNode($r['node']);  // cachelog class currently does not initialize node field
                        $log->setCacheId($r['cache_id']);
                        $log->setUserId($r['user_id']);
                        $log->setType($r['type'], true);
                        $log->setOcTeamComment($r['oc_team_comment']);
                        $log->setDate($r['date']);
                        $log->setText($r['text']);
                        $log->setTextHtml($r['text_html']);
                        $log->setTextHtmlEdit($r['text_htmledit']);
                        $log->setNeedsMaintenance($r['needs_maintenance']);
                        $log->setListingOutdated($r['listing_outdated']);
                        $log->setOwnerNotified(1);

                        if ($simulate) {
                            $logs_restored = true;
                        } else {
                            if (!$log->save()) {
                                $error = "restore";
                            } else {
                                sql(
                                    "INSERT IGNORE INTO `cache_logs_restored`
                                      (`id`, `date_modified`, `cache_id`, `original_id`, `restored_by`)
                                    VALUES ('&1', NOW(), '&2', '&3', '&4')",
                                    $log->getLogId(),
                                    $log->getCacheId(),
                                    $revert_logid,
                                    $login->userid
                                );
                                sql("DELETE FROM `watches_logqueue` WHERE `log_id`='&1'", $log->getLogId());
                                // watches_logqueue entry was created by trigger
                                $logs_processed[] = $log->getLogId();

                                /* no longer needed after implementing picture deletion in removelog.php

                                // log pic deleting is not completely implemented, orphan pictures are    [*p]
                                // left over when directly deleting the log. We try to recover them ...
                                sql("UPDATE `pictures` SET `object_id`='&1' WHERE `object_type`=1 AND `object_id`='&2'",
                                    $log->getLogId(), $revert_logid);

                                // ... and then update the stats:
                                $log->updatePictureStat();
                                 */

                                $logs_restored = true;
                            }
                        }
                    }  // restore deleted

                    $logs_processed[] = $revert_logid;
                }  // not already processed

                if ($error != "") {
                    $restored[$wp]['internal error - could not $error log ' + $r['id'] + "/" + $logid];
                }
                if ($logs_restored) {
                    $restored[$wp]['logs'] = true;
                }
            }  // while (all relevant log records)
            sql_free_result($rs);
        }  // if logs enabled per roptions

        // pictures
        if (in_array("desc", $roptions) || in_array("logs", $roptions)) {
            $rs = sql(
                "SELECT * FROM `pictures_modified`
                        WHERE ((`object_type`=2 AND '&2' AND `object_id`='&3') OR
                                           (`object_type`=1 AND '&1'
                                                  AND IFNULL((SELECT `user_id` FROM `cache_logs` WHERE `id`=`object_id`),(SELECT `user_id` FROM `cache_logs_archived` WHERE `id`=`object_id`)) != '&5'
                                                  /* ^^ ignore changes of own log pics (shouldnt be in pictures_modified, anyway) */
                                                  AND IFNULL((SELECT `cache_id` FROM `cache_logs` WHERE `id`=`object_id`),(SELECT `cache_id` FROM `cache_logs_archived` WHERE `id`=`object_id`)) = '&3'))
                          AND `date_modified`>='&4'
                                    ORDER BY `date_modified` ASC",
                in_array("logs", $roptions) ? 1 : 0,
                in_array("desc", $roptions) ? 1 : 0,
                $cacheid,
                $rdate,
                $user_id
            );

            // We start with the oldest entry and will touch each picture ony once:
            // After restoring its state, it is added to $pics_processed (by its last known id),
            // and all further operations on the same pic are ignored. This prevents unnecessary
            // operations and flooding the _modified table on restore-reverts.
            $pics_processed = array();

            while ($r = sql_fetch_assoc($rs)) {
                $pics_restored = false;

                // the picture id may have changed by multiple delete-and-restores
                $revert_picid = get_current_picid($r['id']);
                if (!in_array($revert_picid, $pics_processed)) {
                    // .. as may have its uuid-based url
                    $revert_url = sql_value(
                        "SELECT `url` FROM `pictures_modified` WHERE `id`='&1'",
                        $r['url'],
                        $revert_picid
                    );
                    $error = "";

                    switch ($r['operation']) {
                        case 'I':
                            if (sql_value("SELECT `id` FROM `pictures` WHERE `id`='&1'", 0, $revert_picid) != 0) {
                                // if it was not already deleted by a later restore operation:
                                // delete added (cache) picture
                                $pic = new picture($revert_picid);
                                if ($simulate) {
                                    $pics_restored = true;
                                } else {
                                    if ($pic->delete(true)) {
                                        $pics_restored = true;
                                    } else {
                                        $error = "delete";
                                    }
                                }
                            }
                            break;

                        case 'U':
                            if (sql_value("SELECT `id` FROM `pictures` WHERE `id`='&1'", 0, $revert_picid) != 0) {
                                // if it was not deleted by a later restore operation:
                                // restore modified (cache) picture properties
                                $pic = new picture($revert_picid);
                                $pic->setTitle($r['title']);
                                $pic->setSpoiler($r['spoiler']);
                                $pic->setDisplay($r['display']);
                                // mappreview flag is not restored, because it seems unappropriate to
                                // advertise for the listing of a vandalizing owner

                                if ($simulate) {
                                    $pics_restored = true;
                                } else {
                                    if ($pic->save(true)) {
                                        $pics_restored = true;
                                    } else {
                                        $error = "update";
                                    }
                                }
                            }
                            break;

                        case 'D':
                            if (sql_value("SELECT `id` FROM `pictures` WHERE `id`='&1'", 0, $revert_picid) == 0) {
                                // if it was not already restored by a later restore operation:
                                // restore deleted picture
                                // id, uuid, date_created and last_modified are set automatically

                                // the referring log's id  may have changed by [multiple] delete-and-restore
                                if ($r['object_type'] == 1) {
                                    $r['object_id'] = get_current_logid($r['object_id']);
                                }

                                // id, uuid, node, date_created, date_modified are automatically set;
                                // url will be set on save;
                                // last_url_check and thumb_last_generated stay at defaults until checked;
                                // thumb_url will be set on thumb creation (old thumb was deleted)
                                $pic = new picture();
                                $pic->setTitle($r['title']);
                                $pic->setObjectId($r['object_id']);
                                $pic->setObjectType($r['object_type']);
                                $pic->setSpoiler($r['spoiler']);
                                $pic->setLocal(1);
                                $pic->setUnknownFormat($r['unknown_format']);
                                $pic->setDisplay($r['display']);
                                // mappreview flag is not restored, because it seems unappropriate to
                                // advertise for the listing of a vandalizing owner

                                if ($simulate) {
                                    $pics_restored = true;
                                } else {
                                    if ($pic->save(true, $revert_picid, $revert_url)) {
                                        $pics_restored = true;
                                        $pics_processed[] = $pic->getPictureId();
                                    } else {
                                        $error = "restore";
                                    }
                                }
                            }
                            break;
                    }  // switch

                    $pics_processed[] = $revert_picid;
                }  // not already processed

                if ($error != "") {
                    $restored[$wp]['internal error - could not $error picture ' . $r['id'] + "/" + $picid] = true;
                }
                if ($pics_restored) {
                    $restored[$wp]['pictures'] = true;
                }
            }  // while (all relevant pic records)

            sql_free_result($rs);
        }  // if pics enabled per roptions
    }  // foreach cache(id)

    sql("SET @restoredby=0");
    sql_slave("SET @restoredby=0");

    return $restored;
}


// determine new id of a log if it has been deleted and restored [multiple times]
function get_current_logid($logid)
{
    do {
        $new_logid = sql_value(
            "SELECT `id` FROM `cache_logs_restored` WHERE `original_id`='&1'",
            0,
            $logid
        );
        if ($new_logid != 0) {
            $logid = $new_logid;
        }
    } while ($new_logid != 0);

    return $logid;
}


// determine new id of a picture if it has been deleted and restored [multiple times]
function get_current_picid($picid)
{
    do {
        $new_picid = sql_value(
            "SELECT `id` FROM `pictures_modified` WHERE `original_id`='&1'",
            0,
            $picid
        );
        if ($new_picid != 0) {
            $picid = $new_picid;
        }
    } while ($new_picid != 0);

    return $picid;
}
