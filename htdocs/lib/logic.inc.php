<?php
/****************************************************************************
 * ./lib/clicompatbase.inc.php
 * Unicode Reminder メモ
 *
 * some common lib1 functions
 ****************************************************************************/

// These functions are needed for notification emails in the recipient's
// language. Looks like there is no way to query this "inline" in LEFT-JOIN-
// statements (the ON clause cannot access user.language).

function get_cachetype_name($cachetype, $language)
{
    return sqlValue(
        "SELECT IFNULL(`stt`.`text`, `cache_type`.`en`)
         FROM `cache_type`
         LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`cache_type`.`trans_id` AND `stt`.`lang`='" . sql_escape($language) . "'
         WHERE `cache_type`.`id`='" . sql_escape($cachetype) . "'",
        ''
    );
}

function get_cachesize_name($cachesize, $language)
{
    return sqlValue(
        "SELECT IFNULL(`stt`.`text`, `cache_size`.`en`)
         FROM `cache_size`
         LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`cache_size`.`trans_id` AND `stt`.`lang`='" . sql_escape($language) . "'
         WHERE `cache_size`.`id`='" . sql_escape($cachesize) . "'",
        ''
    );
}

function get_logtype_name($logtype, $language)
{
    return sqlValue(
        "SELECT IFNULL(`stt`.`text`, `log_types`.`en`)
         FROM `log_types`
         LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`log_types`.`trans_id` AND `stt`.`lang`='" . sql_escape($language) . "'
         WHERE `log_types`.`id`='" . sql_escape($logtype) . "'",
        ''
    );
}
