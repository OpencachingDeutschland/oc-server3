<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  This modules gives you a very usefull SQL debugger for MySQL ...
 ***************************************************************************/

use Oc\Util\CBench;

$sqldebugger = new sqldebugger();

/**
 * Class sqldebugger
 */
class sqldebugger
{
    public $commands = array();
    public $cancel = false;

    /**
     * @return bool
     */
    public function getCancel()
    {
        return $this->cancel;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param $sql
     * @param $dblink
     * @param $bQuerySlave
     * @param $sServer
     *
     * @return resource
     */
    public function execute($sql, $dblink, $bQuerySlave, $sServer)
    {
        global $db;

        if (count($this->commands) >= 1000) {
            $this->cancel = true;

            return mysqli_query($dblink, $sql);
        }

        $command = array();

        $command['sql'] = $sql;
        $command['explain'] = array();
        $command['result'] = array();
        $command['warnings'] = array();
        $command['runtime'] = 0;
        $command['affected'] = 0;
        $command['count'] = - 1;
        $command['mode'] = $db['mode'];
        $command['slave'] = $bQuerySlave;
        $command['server'] = $sServer;
        $command['dblink'] = '' . $dblink;

        $bUseExplain = false;
        $sql = trim($sql);
        $sqlexplain = $sql;

        if (strtoupper(substr($sqlexplain, 0, 7)) == 'DELETE ') {
            $sqlexplain = $this->strip_from($sqlexplain);
        } elseif ((strtoupper(substr($sqlexplain, 0, 12)) == 'INSERT INTO ') ||
            (strtoupper(substr($sqlexplain, 0, 19)) == 'INSERT IGNORE INTO ')
        ) {
            $sqlexplain = $this->strip_temptable($sqlexplain);
        } elseif (strtoupper(substr($sqlexplain, 0, 23)) == 'CREATE TEMPORARY TABLE ') {
            $sqlexplain = $this->strip_temptable($sqlexplain);
        }

        if (strtoupper(substr($sqlexplain, 0, 7)) == 'SELECT ') {
            // we can use EXPLAIN
            $c = 0;
            $rs = mysqli_query($dblink, $sqlexplain);
            $command['count'] = sql_num_rows($rs);
            while ($r = sql_fetch_assoc($rs)) {
                if ($c == 25) {
                    break;
                }
                $command['result'][] = $r;
                $c ++;
            }
            sql_free_result($rs);

            $rs = mysqli_query($dblink, 'EXPLAIN EXTENDED ' . $sqlexplain);
            while ($r = sql_fetch_assoc($rs)) {
                $command['explain'][] = $r;
            }
            sql_free_result($rs);
        }

        // don't use query cache!
        $sql = $this->insert_nocache($sql);

        $bSqlExecution = new CBench;
        $bSqlExecution->start();
        $rsResult = mysqli_query($dblink, $sql);
        $bSqlExecution->stop();
        $bError = ($rsResult == false);
        $command['affected'] = mysqli_affected_rows($dblink);

        $rs = mysqli_query($dblink, 'SHOW WARNINGS');
        while ($r = sql_fetch_assoc($rs)) {
            $command['warnings'][] = $r['Message'];
        }

        $command['runtime'] = $bSqlExecution->diff();

        $this->commands[] = $command;

        return $rsResult;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function strip_temptable($sql)
    {
        $start = stripos($sql, 'SELECT ');

        if ($start === false) {
            return '';
        }

        return substr($sql, $start);
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function strip_from($sql)
    {
        $start = stripos($sql, 'FROM ');

        if ($start === false) {
            return '';
        }

        return 'SELECT * ' . substr($sql, $start);
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function insert_nocache($sql)
    {
        if (strtoupper(substr($sql, 0, 7)) == 'SELECT ') {
            $sql = 'SELECT SQL_NO_CACHE ' . substr($sql, 7);
        }

        return $sql;
    }
}
