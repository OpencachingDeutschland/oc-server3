<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  This script converts an OC productive database into a developer DB.
 *  It removes all data not intended for the public and disables all user
 *  accounts, while keeping the cache listings' status.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/cli.inc.php';

if (!($opt['debug'] & DEBUG_DEVELOPER)) {
    die("This script deletes lot of data and must be run only on development systems.\n");
}
if ($argc != 2 || $argv[1] != 'go') {
    die(
        "This script deletes lot of data. Make sure that you really want to dos this,\n" .
        "and confirm it by adding the parameter 'go'.\n"
    );
}

// ATTENTION: TRUNCATE does not call deletion triggers!

sql('SET @allowdelete=1');

echo "clearing histories\n";
sql('TRUNCATE `caches_attributes_modified`');
sql('TRUNCATE `caches_modified`');
sql('TRUNCATE `cache_adoptions`');
sql('TRUNCATE `cache_coordinates`');
sql('TRUNCATE `cache_countries`');
sql('TRUNCATE `cache_desc_modified`');
sql('TRUNCATE `cache_logs_archived`');
sql('TRUNCATE `cache_logs_modified`');
sql('TRUNCATE `cache_logs_restored`');
sql('TRUNCATE `cache_reports`');
sql('TRUNCATE `cache_status_modified`');
sql('TRUNCATE `email_user`');
sql('TRUNCATE `logentries`');
sql('TRUNCATE `pictures_modified`');
sql('TRUNCATE `saved_texts`');
sql('TRUNCATE `sys_login_stat`');

echo "clearing temporary data\n";
sql('TRUNCATE `cache_maps`');
sql('TRUNCATE `logins`');
sql('TRUNCATE `map2_data`');
sql('TRUNCATE `map2_result`');
sql('TRUNCATE `notify_waiting`');
sql('TRUNCATE `replication`');
sql('TRUNCATE `replication_notimported`');
sql('TRUNCATE `replication_overwrite`');
sql('TRUNCATE `sys_logins`');
sql('TRUNCATE `sys_sessions`');
sql('TRUNCATE `sys_temptables`');
sql('TRUNCATE `watches_logqueue`');
sql('TRUNCATE `watches_notified`');
sql('TRUNCATE `watches_waiting`');
sql('TRUNCATE `xmlsession`');
sql('TRUNCATE `xmlsession_data`');

echo "clearing user data\n";
sql('TRUNCATE `cache_adoption`');
sql('TRUNCATE `cache_ignore`');
sql('CALL sp_updateall_ignorestat(@c)');
sql('DELETE FROM `cache_lists` WHERE `is_public`<2');  // trigger deletes dependent data
sql('TRUNCATE `cache_list_bookmarks`');
sql('TRUNCATE `cache_list_watches`');
sql('TRUNCATE `cache_watches`');
sql('CALL sp_updateall_watchstat(@c)');
sql('CALL sp_updateall_cachelist_counts(@c)');
sql('DELETE FROM `coordinates` WHERE `type`=2');   // personal cache notes and coords
sql('TRUNCATE `queries`');
sql('TRUNCATE `user_options`');
sql(
    "UPDATE `user`
        SET
            `is_active_flag`=0,
            `last_login`=NULL, `password`=NULL, `email`=NULL, `email_problems`=0,
            `first_email_problem`=NULL, `last_email_problem`=NULL, `mailing_problems`=0,
            `accept_mailing`=0, `usermail_send_addr`=0, `latitude`=0, `longitude`=0,
            `last_name`='', `first_name`='', `country`=NULL, `pmr_flag`=0,
            `new_pw_code`=NULL, `new_pw_date`=NULL, `new_email_code`=NULL, `new_email_date`=NULL,
            `new_email`='', `permanent_login_flag`=0, `watchmail_mode`=1,
            `watchmail_hour`=0, `watchmail_nextmail`='', `watchmail_day`=0,
            `activation_code`='', `statpic_logo`=0, `statpic_text`='Opencaching',
            `no_htmledit_flag`=0, `notify_radius`=0, `notify_oconly`=1, `language`='DE',
            `language_guessed`=1, `domain`=NULL, `admin`=0, `data_license`=0,
            `description`='', `desc_htmledit`=1"
);

echo "deleting hidden and locked caches\n";
$rs = sql("SELECT `cache_id` FROM `caches` WHERE `status`>3");
while ($r = sql_fetch_assoc($rs)) {
    echo '.';
    sql("DELETE FROM `caches` WHERE `cache_id`='&1'", $r['cache_id']);
}
echo "\n";
mysql_free_result($rs);

echo "deleting inactive users\n";
$rs = sql(
    "SELECT `user_id`
     FROM `user`
     WHERE `user_id` NOT IN
        (SELECT `user_id` FROM `caches`
         UNION
         SELECT `user_id` FROM `cache_logs`)"
);
while ($r = sql_fetch_assoc($rs)) {
    echo ".";
    sql("DELETE FROM `user` WHERE `user_id`='&1'", $r['user_id']);
}
echo "\n";
mysql_free_result($rs);

echo "clearing OKAPI data\n";
if (sql_table_exists('okapi_vars')) {
    echo "clearing OKAPI data\n";
    sql('TRUNCATE `okapi_authorizations`');
    sql('TRUNCATE `okapi_cache_logs`');
    sql('TRUNCATE `okapi_cache_reads`');
    sql('TRUNCATE `okapi_consumers`');
    sql('TRUNCATE `okapi_nonces`');
    sql('TRUNCATE `okapi_search_results`');
    sql('TRUNCATE `okapi_search_sets`');
    sql('TRUNCATE `okapi_stats_hourly`');
    sql('TRUNCATE `okapi_stats_monthly`');
    sql('TRUNCATE `okapi_stats_temp`');
    sql('TRUNCATE `okapi_tile_caches`');
    sql('TRUNCATE `okapi_tile_status`');
    sql('TRUNCATE `okapi_tokens`');
}

echo "clearing other nonpublic data\n";
sql('TRUNCATE `news`');
$rs = sql("SHOW TABLES WHERE `Tables_in_" . $opt['db']['placeholder']['db'] . "` LIKE '\_%'");
$tables = sql_fetch_column($rs);
foreach ($tables as $table) {
    sql('DROP TABLE ' . $table);
}

echo "done.\n";
