<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/user.class.php');
	$tpl->name = 'mailto';
	$tpl->menuitem = MNU_USER_MAILTO;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=' . urlencode($tpl->target));

	$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0;
	$user = new user($userid);
	if ($user->exist() == false)
		$tpl->error(ERROR_USER_NOT_EXISTS);

	if ($user->getIsActive() == false)
		$tpl->error(ERROR_USER_NOT_ACTIVE);

	if ($user->getEMail() === null || $user->getEMail() == '')
		$tpl->error(ERROR_USER_NO_EMAIL);

	$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : '';
	$text = isset($_REQUEST['text']) ? $_REQUEST['text'] : '';
	if (isset($_REQUEST['emailaddress']))
		$bEmailaddress = ($_REQUEST['emailaddress']==1);
	else
	{
		$own_user = new user($login->userid);
		$bEmailaddress = $own_user->getUsermailSendAddress();
	}

	if (isset($_REQUEST['ok']))
	{
		$bError = false;
		if ($subject == '')
		{
			$bError = true;
			$tpl->assign('errorSubjectEmpty', true);
		}

		if ($text == '')
		{
			$bError = true;
			$tpl->assign('errorBodyEmpty', true);
		}

		if ($bError == false)
		{
			if ($user->sendEMail($login->userid, $subject, $text, $bEmailaddress))
				$tpl->assign('success', true);
			else
				$tpl->assign('errorUnkown', true);
		}
	}

	$tpl->assign('subject', $subject);
	$tpl->assign('text', $text);
	$tpl->assign('emailaddress', $bEmailaddress);
	$tpl->assign('email_problems', $user->getEmailProblems());

	$tpl->assign('userid', $user->getUserId());
	$tpl->assign('username', $user->getUsername());

	$tpl->display();
?>