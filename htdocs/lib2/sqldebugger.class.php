<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  This modules gives you a very usefull SQL debugger for MySQL ...
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/bench.inc.php');
$sqldebugger = new sqldebugger();

class sqldebugger
{
	var $commands = array();
	var $cancel = false;

	function getCancel()
	{
		return $this->cancel;
	}

	function getCommands()
	{
		return $this->commands;
	}

	function execute($sql, $dblink, $bQuerySlave, $sServer)
	{
		global $db;

		if (count($this->commands) >= 1000)
		{
			$this->cancel = true;
			return mysql_query($sql, $dblink);
		}

		$command = array();

		$command['sql'] = $sql;
		$command['explain'] = array();
		$command['result'] = array();
		$command['warnings'] = array();
		$command['runtime'] = 0;
		$command['affected'] = 0;
		$command['count'] = -1;
		$command['mode'] = $db['mode'];
		$command['slave'] = $bQuerySlave;
		$command['server'] = $sServer;
		$command['dblink'] = ''.$dblink;

		$bUseExplain = false;
		$sql = trim($sql);
		$sqlexplain = $sql;

		if (strtoupper(substr($sqlexplain, 0, 7)) == 'DELETE ')
			$sqlexplain = $this->strip_from($sqlexplain);
		else if ((strtoupper(substr($sqlexplain, 0, 12)) == 'INSERT INTO ') || 
							(strtoupper(substr($sqlexplain, 0, 19)) == 'INSERT IGNORE INTO '))
			$sqlexplain = $this->strip_temptable($sqlexplain);
		else if (strtoupper(substr($sqlexplain, 0, 23)) == 'CREATE TEMPORARY TABLE ')
			$sqlexplain = $this->strip_temptable($sqlexplain);

		if (strtoupper(substr($sqlexplain, 0, 7)) == 'SELECT ')
		{
			// we can use EXPLAIN
			$c = 0;
			$rs = mysql_query($sqlexplain, $dblink);
			$command['count'] = sql_num_rows($rs);
			while ($r = sql_fetch_assoc($rs))
			{
				if ($c == 25) break;
				$command['result'][] = $r;
				$c++;
			}
			sql_free_result($rs);

			$rs = mysql_query('EXPLAIN EXTENDED ' . $sqlexplain, $dblink);
			while ($r = sql_fetch_assoc($rs))
			{
				$command['explain'][] = $r;
			}
			sql_free_result($rs);
		}

		// dont use query cache!
		$sql = $this->insert_nocache($sql);

		$bSqlExecution = new Cbench; 
		$bSqlExecution->start();
		$rsResult = mysql_query($sql, $dblink);
		$bSqlExecution->stop();
		$bError = ($rsResult == false);
		$command['affected'] = mysql_affected_rows($dblink);

		$rs = mysql_query('SHOW WARNINGS', $dblink);
		while ($r = sql_fetch_assoc($rs))
			$command['warnings'][] = $r['Message'];

		$command['runtime'] = $bSqlExecution->Diff();

		$this->commands[] = $command;

		return $rsResult;
	}

	function strip_temptable($sql)
	{
		$start = stripos($sql, 'SELECT ');

		if ($start === false)
			return '';

		return substr($sql, $start);
	}

	function strip_from($sql)
	{
		$start = stripos($sql, 'FROM ');

		if ($start === false)
			return '';

		return 'SELECT * ' . substr($sql, $start);
	}

	function insert_nocache($sql)
	{
		if (strtoupper(substr($sql, 0, 7)) == 'SELECT ')
			$sql = 'SELECT SQL_NO_CACHE ' . substr($sql, 7);

		return $sql;
	}
}
?>