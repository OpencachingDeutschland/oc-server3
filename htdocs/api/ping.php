<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$opt['rootpath'] = '../';
	require($opt['rootpath'] . 'lib2/web.inc.php');

	header('Content-type: text/html; charset=utf-8');
	echo sql_value("SELECT NOW()","");

?>