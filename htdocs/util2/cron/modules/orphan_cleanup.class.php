<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Deletes orphan data which are left over due to software bugs or
 *  system failures.
 ***************************************************************************/

checkJob(new orphan_cleanup());

class orphan_cleanup
{
    public $name = 'orphan_cleanup';
    public $interval = 86400;   // once per day

    public function run()
    {
        // cleanup XML session data
        sql_temp_table('tmpsessiondata');
        sql(
            'CREATE TEMPORARY TABLE &tmpsessiondata ENGINE=MEMORY
             SELECT DISTINCT `xmlsession_data`.`session_id` FROM `xmlsession_data`
             LEFT JOIN `xmlsession` ON `xmlsession`.`id`=`xmlsession_data`.`session_id`
             WHERE `xmlsession`.`id` IS NULL'
        );
        $count = sql_value(
            'SELECT COUNT(*) FROM `xmlsession_data`
             WHERE `session_id` IN (SELECT `session_id` FROM &tmpsessiondata)',
            0
        );
        if ($count) {
            sql(
                "DELETE FROM `xmlsession_data`
                 WHERE `session_id` IN (SELECT `session_id` FROM &tmpsessiondata)"
            );
            echo 'orphan_cleanup: dropped ' . $count . " record(s) from xmlsession_data\n";
        }
        sql_drop_temp_table('tmpsessiondata');

        // cleanup map data
        sql_temp_table('tmpsessiondata');
        sql(
            'CREATE TEMPORARY TABLE &tmpsessiondata ENGINE=MEMORY
             SELECT DISTINCT `map2_data`.`result_id` FROM `map2_data`
             LEFT JOIN `map2_result` ON `map2_result`.`result_id`=`map2_data`.`result_id`
             WHERE `map2_result`.`result_id` IS NULL'
        );
        $count = sql_value(
            "SELECT COUNT(*) FROM `map2_data`
             WHERE `result_id` IN (SELECT `result_id` FROM &tmpsessiondata)",
            0
        );
        if ($count) {
            sql(
                "DELETE FROM `map2_data`
                 WHERE `result_id` IN (SELECT `result_id` FROM &tmpsessiondata)"
            );
            echo 'orphan_cleanup: dropped ' . $count . " record(s) from map2_data\n";
        }
        sql_drop_temp_table('tmpsessiondata');
    }

}
