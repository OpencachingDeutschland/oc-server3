<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require_once('./lib2/web.inc.php');
	require_once('./lib2/logic/user.class.php');
	require_once('./lib2/logic/useroptions.class.php');

	$tpl->name = 'mydetails';
	$tpl->menuitem = MNU_MYPROFILE_DETAILS;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	$action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : 'view';
	if ($action != 'change' &&  $action != 'view') $action = 'view';

	if ($action == 'change')
	{
		$tpl->menuitem = MNU_MYPROFILE_DETAILS_EDIT;
		change();
	}
	else
	{
		display();
	}

exit;

function change()
{
	global $tpl, $login, $opt;

	if (isset($_REQUEST['cancel']))
		$tpl->redirect('mydetails.php');

	$useroptions = new useroptions($login->userid);

	if (isset($_REQUEST['save']))
	{
		$rs = sql('SELECT `id` FROM `profile_options` ORDER BY `id`');

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
			if (strlen($value) > 2000 && $opt['logic']['enableHTMLInUserDescription'] != true)
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
	}

	assignFromDB($login->userid);
	$tpl->assign('edit', true);
	$tpl->display();
}

function display()
{
	global $tpl, $login;
	assignFromDB($login->userid);
	$tpl->display();
}

function assignFromDB($userid)
{
	global $tpl, $opt;

	$rs = sql("SELECT `p`.`id`, IFNULL(`tt`.`text`, `p`.`name`) AS `name`, `p`.`default_value`, `p`.`check_regex`, `p`.`option_order`, `u`.`option_visible`, `p`.`internal_use`, `p`.`option_input`, IFNULL(`u`.`option_value`, `p`.`default_value`) AS `option_value`
		           FROM `profile_options` AS `p`
		      LEFT JOIN `user_options` AS `u` ON `p`.`id`=`u`.`option_id` AND (`u`.`user_id` IS NULL OR `u`.`user_id`='&1')
		      LEFT JOIN `sys_trans` AS `st` ON `p`.`trans_id`=`st`.`id` AND `p`.`name`=`st`.`text`
		      LEFT JOIN `sys_trans_text` AS `tt` ON `st`.`id`=`tt`.`trans_id` AND `tt`.`lang`='&2'
		       ORDER BY `p`.`internal_use` DESC, `p`.`option_order`", 
		                $userid+0, 
		                $opt['template']['locale']);
	$tpl->assign_rs('useroptions', $rs);
	sql_free_result($rs);
}
?>
