<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

checkJob(new slave_cleanup());

class slave_cleanup
{
    public $name = 'slave_cleanup';
    public $interval = 300;

    public function run()
    {
        global $opt;
        $rs = sql(
            "SELECT `id`
            FROM `sys_repl_slaves`
            WHERE `active` = 1
            AND `online` = 1
            AND (TIMESTAMP(NOW())-TIMESTAMP(`last_check`)+`time_diff`<'&1')",
            $opt['db']['slave']['max_behind']
        );
        while ($r = sql_fetch_assoc($rs)) {
            $this->cleanup_slave($r['id']);
        }
        sql_free_result($rs);

        $this->cleanup_slave(- 1);
    }

    public function cleanup_slave($slaveId)
    {
        // ensure old slave is disconnected
        sql_disconnect_slave();

        // connect the slave
        if ($slaveId == - 1) {
            sql_connect_master_as_slave();
        } else {
            sql_connect_slave($slaveId);
        }

        $this->cleanup_mapresult2($slaveId);

        // disconnect slave
        sql_disconnect_slave();
    }

    public function cleanup_mapresult2($slaveId)
    {
        global $opt;

        // cleanup old entries
        $rs = sql(
            "SELECT SQL_BUFFER_RESULT `result_id`
            FROM `map2_result`
            WHERE DATE_ADD(`date_created`, INTERVAL '&1' SECOND)<NOW()",
            $opt['map']['maxcacheage']
        );
        while ($r = sql_fetch_assoc($rs)) {
            sql("DELETE FROM `map2_result` WHERE `result_id`='&1'", $r['result_id']);
        }
        sql_free_result($rs);

        // now reduce table size? (29 bytes is the average row size)
        if (sql_value_slave("SELECT COUNT(*) FROM `map2_data`", 0) > $opt['map']['maxcachesize'] / 29) {
            while (sql_value_slave("SELECT COUNT(*) FROM `map2_data`", 0) > $opt['map']['maxcachereducedsize'] / 29) {
                $resultId = sql_value(
                    "SELECT `result_id` FROM `map2_result` WHERE `slave_id`='&1'
                    ORDER BY `date_lastqueried` DESC LIMIT 1",
                    0,
                    $slaveId
                );
                if ($resultId == 0) {
                    return;
                }
                sql("DELETE FROM `map2_result` WHERE `result_id`='&1'", $resultId);
            }
        }

        $nMinId = sql_value("SELECT MIN(`result_id`) FROM `map2_result`", 0);
        if ($nMinId == 0) {
            sql("DELETE FROM `map2_data`");
        } else {
            sql("DELETE FROM `map2_data` WHERE `result_id`<'&1'", $nMinId);
        }
    }
}
