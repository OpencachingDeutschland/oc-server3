<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	function set_oconly81_tpldata($userid)
	{
		global $tpl;

		$terr = $tsum = array(2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0, 8=>0, 9=>0, 10=>0);
		$stat81 = array(2=>$terr, 3=>$terr, 4=>$terr, 5=>$terr, 6=>$terr, 7=>$terr, 8=>$terr, 9=>$terr, 10=>$terr);

		if ($userid > 0)
		{
			$rs = sql("
				SELECT `difficulty`, `terrain`, COUNT(*) AS `count`
				FROM `cache_logs`
				INNER JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
				INNER JOIN `caches_attributes` ON `caches_attributes`.`cache_id`=`cache_logs`.`cache_id` AND `caches_attributes`.`attrib_id`=6
				WHERE `cache_logs`.`user_id`='&1' AND `cache_logs`.`type` = 1
				GROUP BY `difficulty`, `terrain`",
				$userid);
		}
		else
		{
			$rs = sql("
				SELECT `difficulty`, `terrain`, COUNT(*) AS `count`
				FROM `caches`
				INNER JOIN `caches_attributes` ON `caches_attributes`.`cache_id`=`caches`.`cache_id` AND `caches_attributes`.`attrib_id`=6
				WHERE `status`=1
				GROUP BY `difficulty`, `terrain`");
		}
		$maxcount = 0;

		while ($r = sql_fetch_assoc($rs))
		{
			$stat81[$r['difficulty']][$r['terrain']] = $r['count'];
			$maxcount = max($maxcount, $r['count']);
			$tsum[$r['terrain']] += $r['count'];
		}
		sql_free_result($rs);
		$tpl->assign('stat81',$stat81);
		$tpl->assign('stat81_maxcount',max(10,$maxcount));
		$tpl->assign('stat81_tsum', $tsum);
	}

?>