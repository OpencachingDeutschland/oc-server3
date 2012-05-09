<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  - check all slaves and manage table sys_repl_slaves
 *  - check all slaves and purge master logs
 *  - send warning when too many logs stay on master (file size)
 ***************************************************************************/

checkJob(new replication_monitor());

class replication_monitor
{
	var $name = 'replication_monitor';
	var $interval = 0;
	//var $interval = 60;

	function run()
	{
		global $opt;
		$known_ids = array();

		foreach ($opt['db']['slaves'] AS $k => $v)
		{
			$this->check_slave($k);
			$known_ids[] = "'" . sql_escape($k) . "'";
		}

		if (count($known_ids) > 0)
			sql("DELETE FROM `sys_repl_slaves` WHERE `id` NOT IN (" .  implode(',', $known_ids) . ")");
		else
			sql("DELETE FROM `sys_repl_slaves`");

		// now, clean up sys_repl_exclude
		sql("DELETE FROM `sys_repl_exclude` WHERE `datExclude`<DATE_SUB(NOW(), INTERVAL '&1' SECOND)", $opt['db']['slave']['max_behind']);
	}

	function check_slave($id)
	{
		global $opt;
		$nActive = 0;
		$nOnline = 0;
		$sLogName = '';
		$sLogPos = '';
		$nTimeDiff = -1;

		$slave = $opt['db']['slaves'][$id];
		if ($slave['active'] == true)
		{
			$nActive = 1;

			// connect
			$dblink = @mysql_connect($slave['server'], $slave['username'], $slave['password']);
			if ($dblink !== false)
			{
				if (mysql_select_db($opt['db']['placeholder']['db'], $dblink))
				{
					// read slave time
					$rs = mysql_query("SELECT `data` FROM `sys_repl_timestamp`", $dblink);
					if ($rs !== false)
					{
						$rTime = mysql_fetch_assoc($rs);
						mysql_free_result($rs);

						// read current master db time
						$nMasterTime = sql_value("SELECT NOW()", null);

						$nTimeDiff = strtotime($nMasterTime) - strtotime($rTime['data']);
						if ($nTimeDiff < $opt['db']['slave']['max_behind'])
						{
							$nOnline = 1;
						}
					}

					// update logpos
					$rs = mysql_query("SHOW SLAVE STATUS");
					$r = mysql_fetch_assoc($rs);
					mysql_free_result($rs);
					$sLogName = $r['Master_Log_File'];
					$sLogPos = $r['Read_Master_Log_Pos'];
				}
				mysql_close($dblink);
			}
		}

		// only-flag changed?
		if ($nOnline != sql_value("SELECT `online` FROM `sys_repl_slaves` WHERE `id`='&1'", 0, $id))
		{
			mail($opt['db']['error']['mail'], "MySQL Slave Server Id " . $id . " (" . $slave['server'] . ") is now " . (($nOnline!=0)?'Online':'Offline'), '');
		}

		sql("INSERT INTO `sys_repl_slaves` (`id`, `server`, `active`, `weight`, `online`, `last_check`, `current_log_name`, `current_log_pos`)
		                            VALUES ('&1', '&2', '&3', '&4', '&5', NOW(), '&6', '&7')
		           ON DUPLICATE KEY UPDATE `server`='&2', `active`='&3', `weight`='&4', `online`='&5', `last_check`=NOW(), `current_log_name`='&6', `current_log_pos`='&7'",
		                            $id, $slave['server'], $nActive, $slave['weight'], $nOnline, $sLogName, $sLogPos);

		// update time_diff?
		if ($nTimeDiff != -1)
			sql("UPDATE `sys_repl_slaves` SET `time_diff`='&1' WHERE `id`='&2'", $nTimeDiff, $id);
	}
}
?>