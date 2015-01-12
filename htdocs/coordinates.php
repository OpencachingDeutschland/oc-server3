<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/coordinate.class.php');

	$tpl->name = 'coordinates';
	$tpl->popup = true;

	$lat_float = 0;
	if (isset($_REQUEST['lat']))
		$lat_float += $_REQUEST['lat'];

	$lon_float = 0;
	if (isset($_REQUEST['lon']))
		$lon_float += $_REQUEST['lon'];

	$coord = new coordinate($lat_float, $lon_float);

	$tpl->assign('coordDeg', $coord->getDecimal());
	$tpl->assign('coordDegMin', $coord->getDecimalMinutes());
	$tpl->assign('coordDegMinSec', $coord->getDecimalMinutesSeconds());
	$tpl->assign('coordUTM', $coord->getUTM());
	$tpl->assign('coordGK', $coord->getGK());
	$tpl->assign('coordRD', $coord->getRD());
	$tpl->assign('showRD', ($coord->nLat >= 45 && $coord->nLat <= 57 && $coord->nLon >= 0 && $coord->nLon <= 15));
	$tpl->assign('coordQTH', $coord->getQTH());
	$tpl->assign('coordSwissGrid', $coord->getSwissGrid());
	$tpl->assign('coordW3Wde', $coord->getW3W('de'));
	$tpl->assign('coordW3Wen', $coord->getW3W('en'));

	// wp gesetzt?
	$wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
	if ($wp != '')
	{
		$rs = sql("SELECT `caches`.`name`, `user`.`username` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id` WHERE `cache_status`.`allow_user_view`=1 AND `caches`.`wp_oc`='&1'", $wp);
		if ($r = sql_fetch_array($rs))
		{
			$tpl->assign('owner', $r['username']);
			$tpl->assign('cachename', $r['name']);
		}
		sql_free_result($rs);
	}
	$tpl->assign('wp', $wp);

	$tpl->display();
?>