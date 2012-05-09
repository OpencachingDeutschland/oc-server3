<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Cleanup the table sys_temptables from entries of dead threads
 *
 *                         run it once a day
 *
 ***************************************************************************/

checkJob(new cleanup_temptables());

class cleanup_temptables
{
	var $name = 'cleanup_temptables';
	var $interval = 86400;

	function run()
	{
		$nIds = array();
		$rs = sqlf("SHOW PROCESSLIST");
		while ($r = sql_fetch_assoc($rs))
			$nIds[$r['Id']] = $r['Id'];
		sql_free_result($rs);

		$rs = sqlf("SELECT DISTINCT `threadid` FROM `sys_temptables`");
		while ($r = sql_fetch_assoc($rs))
		{
			if (!isset($nIds[$r['threadid']]))
				sqlf("DELETE FROM `sys_temptables` WHERE `threadid`='&1'", $r['threadid']);
		}
		sql_free_result($rs);	
	}
}
?>