<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require_once('lib2/web.inc.php');
	require_once('lib2/logic/user.class.php');
	require_once('lib2/logic/useroptions.class.php');
	require_once('lib2/OcHTMLPurifier.class.php');

	$tpl->name = 'mydetails';
	$tpl->menuitem = MNU_MYPROFILE_DETAILS;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	if (isset($_REQUEST['cancel']))
		$tpl->redirect('mydetails.php');

	$action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : 'view';
	if ($action != 'change' && $action != 'changetext' && $action != 'view') $action = 'view';

	if ($action == 'change')
		change();
	else if ($action == 'changetext')
		changetext();
	else
		display();

exit;


function change()
{
	global $tpl, $login, $opt;

	$useroptions = new useroptions($login->userid);

	if (isset($_REQUEST['save']))
	{
		$rs = sql('SELECT `id` FROM `profile_options` WHERE `optionset`=1 ORDER BY `id`');
		$bError = false;
		$error = ': ';
		$errorlen = ': ';
		$bErrorlen = false;

		while ($record = sql_fetch_array($rs))
		{
			$id = $record['id'];
			$vis = isset($_REQUEST['chk' . $id]) ? $_REQUEST['chk' . $id]+0 : 0;
			$value = isset($_REQUEST['inp' . $id]) ? $_REQUEST['inp' . $id] : '';
			if ($vis != 1) $vis = 0;

			$useroptions->setOptVisible($id, $vis);
			if (strlen($value) > 2000)
			{
				$errorlen .= $useroptions->getOptName($id);
				$bErrorlen = true;
			}
			else
			{
				if (!$useroptions->setOptValue($id, $value))
				{
					$error .= $useroptions->getOptName($id) . ', ';
					$bError = true;
				}
			}
		}
	
		sql_free_result($rs);

		$error = substr($error, 0, -2);

		$tpl->assign('error', $bError);
		$tpl->assign('errormsg', $error);
		$tpl->assign('errorlen', $bErrorlen);
		$tpl->assign('errormsglen', $errorlen);

		if (!$useroptions->save())
		{
			$bError = true;
			$tpl->assign('errorUnknown', true);
		}
		else if (!$bError)
		  $tpl->redirect('mydetails.php');
	}

	assignFromDB($login->userid,false);
	$tpl->assign('edit', true);
	$tpl->display();
}


function changetext()
{
	global $tpl, $login, $opt;

	if (isset($_REQUEST['save']))
	{
		$purifier = new OcHTMLPurifier($opt);
		$desctext = isset($_REQUEST['desctext']) ? $purifier->purify($_REQUEST['desctext']) : "";
		$desc_htmledit = isset($_REQUEST['descMode']) && $_REQUEST['descMode'] == '2' ? '0' : '1';
		sql("
			UPDATE `user`
			SET `description`='&2', `desc_htmledit`='&3'
			WHERE `user_id`='&1'",
			$login->userid, $desctext, $desc_htmledit);
	  $tpl->redirect('mydetails.php');
	}
	else
	{
		$tpl->name = 'mydescription';
		assignFromDB($login->userid,true);
		$tpl->display();
	}
}


function display()
{
	global $tpl, $login;
	assignFromDB($login->userid,false);
	$tpl->display();
}


function assignFromDB($userid,$include_editor)
{
	global $tpl, $opt, $smilies, $_REQUEST;

	$rs = sql("SELECT `p`.`id`, IFNULL(`tt`.`text`, `p`.`name`) AS `name`, `p`.`default_value`, `p`.`check_regex`, `p`.`option_order`, `u`.`option_visible`, `p`.`internal_use`, `p`.`option_input`, IFNULL(`u`.`option_value`, `p`.`default_value`) AS `option_value`
		           FROM `profile_options` AS `p`
		      LEFT JOIN `user_options` AS `u` ON `p`.`id`=`u`.`option_id` AND (`u`.`user_id` IS NULL OR `u`.`user_id`='&1')
		      LEFT JOIN `sys_trans` AS `st` ON `p`.`trans_id`=`st`.`id` AND `p`.`name`=`st`.`text`
		      LEFT JOIN `sys_trans_text` AS `tt` ON `st`.`id`=`tt`.`trans_id` AND `tt`.`lang`='&2'
		          WHERE `optionset`=1
		       ORDER BY `p`.`internal_use` DESC, `p`.`option_order`", 
		                $userid+0, 
		                $opt['template']['locale']);
	$tpl->assign_rs('useroptions', $rs);
	sql_free_result($rs);

	if (isset($_REQUEST['desctext']))
		$tpl->assign('desctext', $_REQUEST['desctext']);
	else
		$tpl->assign('desctext',
			sql_value("SELECT `description` FROM `user` WHERE `user_id`='&1'", '', $userid+0));

	// Use the same descmode values here like in log and cachedesc editor:
	if ($include_editor)
	{
		if (isset($_REQUEST['descMode']))
			$descMode = min(3,max(2,$_REQUEST['descMode']+0));
		else
		{
			if (sql_value("SELECT `desc_htmledit` FROM `user` WHERE `user_id`='&1'", 0, $userid+0))
				$descMode = 3;
			else
				$descMode = 2;
		}
		if ($descMode == 3)
		{
			$tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
			$tpl->add_header_javascript('resource2/tinymce/config/user.js.php?lang='.strtolower($opt['template']['locale']));
		}
		$tpl->add_header_javascript('templates2/' . $opt['template']['style'] . '/js/editor.js');
		$tpl->assign('descMode',$descMode);
	}
}
?>
