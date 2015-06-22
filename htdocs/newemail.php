<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$disable_verifyemail = true;
	require('./lib2/web.inc.php');
	require_once('./lib2/logic/user.class.php');
	$tpl->name = 'newemail';
	$tpl->menuitem = MNU_MYPROFILE_DATA;

	$login->verify();

	if ($login->userid == 0)
		$tpl->redirect('login.php?target=newemail.php');

	$user = new user($login->userid);
	$tpl->assign('newemail', $user->getNewEMail());

	if (isset($_REQUEST['request']))
	{
		$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
		$tpl->assign('email', $email);

		$bError = false;
		if (mb_strtolower($user->getEMail()) == mb_strtolower($email))
		{
			$tpl->assign('emailErrorSame', true);
			$bError = true;
		}

		if ($bError == false && !is_valid_email_address($email))
		{
			$tpl->assign('emailErrorInvalid', true);
			$bError = true;
		}

		if ($bError == false && $user->existEMail($email))
		{
			$tpl->assign('emailErrorExists', true);
			$bError = true;
		}

		if ($bError == false && $user->requestNewEMail($email))
		{
			$tpl->assign('emailRequested', true);
			$tpl->assign('newemail', $email);
		}
		else
		{
			if ($bError == false)
			{
				$tpl->assign('emailErrorUnkown', true);
				$bError = true;
			}
		}
	}
	elseif (isset($_REQUEST['change']))
	{
		$code = isset($_REQUEST['code']) ? mb_trim($_REQUEST['code']) : '';
		$tpl->assign('code', $code);

		$bError = false;
		
		if ($user->getNewEMail() === null)
		{
			$tpl->assign('codeErrorNoNewEMail', true);
			$bError = true;
		}
		
		if ($bError == false && $user->getNewEMailDate() < time() - 3*24*60*60)
		{
			$tpl->assign('codeErrorExpired', true);
			$bError = true;
		}

		if ($bError == false && mb_strtolower($user->getNewEMailCode()) != mb_strtolower($code))
		{
			$tpl->assign('codeErrorNotMatch', true);
			$bError = true;
		}
		
		if ($bError == false)
		{
			$email = $user->getNewEMail();

			if ($bError == false && $user->existEMail($email))
			{
				$tpl->assign('codeErrorEMailExists', true);
				$bError = true;
			}

			if ($bError == false && !$user->setEMail($email))
			{
				$tpl->assign('codeErrorUnkown', true);
				$bError = true;
			}

			if ($bError == false)
			{
				if (!$user->save())
				{
					$tpl->assign('codeErrorUnkown', true);
					$bError = true;
				}
				else
				{
					$tpl->assign('codeChanged', true);
					$user->clearNewEMailCode();
					// $tpl->assign('newemail', '');
					// $tpl->assign('code', '');
					$user->confirmEmailAddress();
				}
			}
		}
	}

	$tpl->display();
?>