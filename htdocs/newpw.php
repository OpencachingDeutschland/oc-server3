<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$disable_verifyemail = true;
	require('./lib2/web.inc.php');
	require_once('./lib2/logic/user.class.php');
	$tpl->name = 'newpw';

	$login->verify();

	if ($login->userid != 0)
	{
		$tpl->menuitem = MNU_MYPROFILE_DATA_PASSWORD;
		$user = new user($login->userid);
		$tpl->assign('emailrq', $user->getEMail());
		$target = 'myprofile.php';
	}
	else
	{
		$tpl->menuitem = MNU_LOGIN_NEWPW;
		$target = 'login.php';
	}

	if (isset($_REQUEST['cancel']))
		$tpl->redirect($target);

	if (isset($_REQUEST['rqcode']))
	{
		$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
		$user = user::fromEMail($email);

		if ($user !== null)
		{
			if ($user->requestNewPWCode())
			{
				$tpl->assign('emailRequested', true);
				$tpl->assign('emailch', $email);
			}
			else
				$tpl->assign('emailErrorUnknown', true);
		}
		else
		{
			$tpl->assign('emailErrorNotFound', true);
		}

		$tpl->assign('emailrq', $email);
	}
	else if (isset($_REQUEST['changepw']))
	{
		$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
		$code = isset($_REQUEST['code']) ? mb_trim($_REQUEST['code']) : '';
		$password1 = isset($_REQUEST['password1']) ? mb_trim($_REQUEST['password1']) : '';
		$password2 = isset($_REQUEST['password2']) ? mb_trim($_REQUEST['password2']) : '';
		
		$bError = false;
		$user = user::fromEMail($email);
		if ($user === null)
		{
			$tpl->assign('emailRqErrorNotFound', true);
			$bError = true;
		}
		else
		{
			if ($user !== null && ($user->getNewPWDate() < time() - 3*24*60*60))
			{
				$tpl->assign('codeErrorDate', true);
				$bError = true;
			}
			else if ($user !== null && mb_strtoupper($user->getNewPWCode()) != mb_strtoupper($code))
			{
				$tpl->assign('codeError', true);
				$bError = true;
			}

			if ($password1 != $password2)
			{
				$tpl->assign('passwordNotMatch', true);
				$bError = true;
			}
			else if ($user !== null && !$user->setPassword($password1))
			{
				$tpl->assign('passwordError', true);
				$bError = true;
			}

			if ($user->getIsActive() == false)
			{
				$tpl->assign('notActiveError', true);
				$bError = true;
			}

			if ($bError == false)
			{
				$user->clearNewPWCode();
				if (!$user->save())
					$tpl->assign('errorUnknown', true);
				else
					$tpl->assign('passwordChanged', true);
				$code = '';
			}
		}

		$tpl->assign('emailrq', $email);
		$tpl->assign('emailch', $email);
		$tpl->assign('code', $code);
	}

	$tpl->display();
?>