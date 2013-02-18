<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$disable_verifyemail = true;
	require_once('lib2/web.inc.php');
	require_once('lib2/logic/user.class.php');
	require_once('lib2/logic/useroptions.class.php');

	$tpl->name = 'verifyemail';
	$tpl->menuitem = MNU_VERIFY_EMAIL;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	$orgpage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'index.php'; 
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'ask';

	if ($action == $translate->t("Enter new email address",'','',0))
		$action = 'change';
	else if ($action == $translate->t("Confirm this email address",'','',0))
		$action = 'confirm';
	else
		$action = 'ask';

	$user = new user($login->userid);

	if ($action == 'change')
	{
		$tpl->redirect('newemail.php');
	}
	else if ($action == 'confirm')
	{
		$user->confirmEmailAddress();
		$tpl->redirect($orgpage);
	}
	else
	{
		$tpl->assign('emailadr', $user->getEMail());
		if ($user->missedDataLicenseMail())
			$tpl->assign('datalicensemail', "<br /><br />" . file_get_contents("resource2/ocstyle/misc/datalicensemail.html"));
		$tpl->assign('orgpage',$orgpage);
	}

	$tpl->display();
?>
