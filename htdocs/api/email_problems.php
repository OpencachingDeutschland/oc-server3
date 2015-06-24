<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$opt['rootpath'] = '../';
	require($opt['rootpath'] . 'lib2/web.inc.php');

	if ($opt['logic']['api']['email_problems']['key'] &&
	    isset($_REQUEST['key']) &&
	    $opt['logic']['api']['email_problems']['key'] == $_REQUEST['key'])
	{
		$rs = sql("SELECT `user_id`, `email_problems` FROM `user` WHERE `email_problems`");
		while ($r = sql_fetch_assoc($rs))
			echo $r['user_id'] . ' ' . $r['email_problems'] . "\n";
		sql_free_result($rs);
	}

?>