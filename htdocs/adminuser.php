<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require('./lib2/logic/user.class.php');

	$tpl->name = 'adminuser';
	$tpl->menuitem = MNU_ADMIN_USER;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	if (($login->admin & ADMIN_USER) != ADMIN_USER)
		$tpl->error(ERROR_NO_ACCESS);
	
	if (isset($_REQUEST['success']) && $_REQUEST['success'])
	  $tpl->assign('success','1'); 

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'display';

	if ($action == 'searchuser')
	{
		searchUser();
	}
	else if ($action == 'sendcode')
	{
		sendCode();
	}
	else if ($action == 'formaction')
	{
		formAction();
	}
	else if ($action == 'display')
		$tpl->display();

	$tpl->error(ERROR_UNKNOWN);

function sendCode()
{
	global $tpl;

	$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0;

	$user = new user($userid);
	if ($user->exist() == false)
		$tpl->error(ERROR_UNKNOWN);

	// send a new confirmation
	$user->sendRegistrationCode();

	$tpl->redirect('adminuser.php?action=searchuser&msg=sendcodecommit&username=' . urlencode($user->getUsername()));
}

function formAction()
{
	global $tpl, $login, $translate;

	$commit = isset($_REQUEST['chkcommit']) ? $_REQUEST['chkcommit']+0 : 0;
	$delete = isset($_REQUEST['chkdelete']) ? $_REQUEST['chkdelete']+0 : 0;
	$disable = isset($_REQUEST['chkdisable']) ? $_REQUEST['chkdisable']+0 : 0;
	$emailproblem = isset($_REQUEST['chkemail']) ? $_REQUEST['chkemail']+0 : 0;
	$datalicense = isset($_REQUEST['chkdl']) ? $_REQUEST['chkdl']+0 : 0;
	$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0;
	$disduelicense = isset($_REQUEST['chkdisduelicense']) ? $_REQUEST['chkdisduelicense']+0 : 0;

	$user = new user($userid);
	if ($user->exist() == false)
		$tpl->error(ERROR_UNKNOWN);
	$username = $user->getUsername();

	if ($delete + $disable + $disduelicense > 1)
		$tpl->error($translate->t('Please select only one of the delete/disable options!','','',0));

	if ($commit == 0)
		$tpl->error($translate->t('You have to check that you are sure!','','',0));

	if ($disduelicense == 1) 
	{
		$errmesg = $user->disduelicense();
		if ($errmesg !== true)
			$tpl->error($errmesg);
	} 
	else if ($disable == 1)
	{
		if ($user->disable() == false)
			$tpl->error(ERROR_UNKNOWN);
	}
	else if ($delete == 1)
	{
		if ($user->delete() == false)
			$tpl->error(ERROR_UNKNOWN);
	}
	else if ($emailproblem == 1)
	{
		$user->addEmailProblem($datalicense);
	}
	
	$tpl->redirect('adminuser.php?action=searchuser&username=' . urlencode($username) . 
								 '&success=' . ($disduelicense + $disable));  
}

function searchUser()
{
	global $tpl;

	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';

	$tpl->assign('username', $username);
	$tpl->assign('msg', $msg);
	
	$rs = sql("SELECT `user_id`, `username`, `email`, `email_problems`, `date_created`, `last_modified`, `is_active_flag`, `activation_code`, `first_name`, `last_name` FROM `user` WHERE `username`='&1' OR `email`='&1'", $username);
	$r = sql_fetch_assoc($rs);
	sql_free_result($rs);
	if ($r == false)
	{
		$tpl->assign('error', 'userunknown');
		$tpl->display();
	}

	$tpl->assign('showdetails', true);

	$r['hidden'] = sql_value("SELECT COUNT(*) FROM `caches` WHERE `user_id`='&1'", 0, $r['user_id']);
	$r['hidden_active'] = sql_value("SELECT COUNT(*) FROM `caches` WHERE `user_id`='&1' AND `status`=1", 0, $r['user_id']);
	$r['logentries'] = sql_value("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1'", 0, $r['user_id']);
	
	$r['last_known_login'] = sql_value("SELECT MAX(`last_login`) FROM `sys_sessions` WHERE `user_id`='&1'", 0, $r['user_id']);

	$tpl->assign('user', $r);

	$user = new user($r['user_id']);
	if (!$user->exist())
		$tpl->error(ERROR_UNKNOWN);
	$tpl->assign('candisable', $user->canDisable());
	$tpl->assign('candelete', $user->canDelete());
	$tpl->assign('cansetemail', !$user->missedDataLicenseMail());

	$tpl->display();
}
?>