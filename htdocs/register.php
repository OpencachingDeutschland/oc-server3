<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once($opt['rootpath'] . 'lib2/logic/user.class.php');
	require_once($opt['rootpath'] . 'lib2/logic/countriesList.class.php');
	require_once($opt['rootpath'] . 'lib2/mail.class.php');

	$tpl->name = 'register';
	$tpl->menuitem = MNU_START_REGISTER;

	$countriesList = new countriesList();

	// Read register informations
	$show_all_countries = isset($_POST['show_all_countries']) ? $_POST['show_all_countries']+0 : 0;
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
	$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
	$password = isset($_POST['password1']) ? $_POST['password1'] : '';
	$password2 = isset($_POST['password2']) ? $_POST['password2'] : '';
	$email = isset($_POST['email']) ? mb_trim($_POST['email']) : '';
	$country = isset($_POST['country']) ? $_POST['country'] : 'XX';
	$tos = isset($_POST['TOS']) ? ($_POST['TOS'] == 'ON') : false;

	if (isset($_POST['show_all_countries_submit']))
		$show_all_countries = 1;
	else if (isset($_POST['submit']))
	{
		$bError = false;

		$user = new user(ID_NEW);
		if (!$user->setEMail($email))
		{
			$bError = true;
			$tpl->assign('error_email_not_ok', 1);
		}

		if (!$user->setUsername($username))
		{
			$bError = true;
			$tpl->assign('error_username_not_ok', 1);
		}

		if (!$user->setFirstName($first_name))
		{
			$bError = true;
			$tpl->assign('error_first_name_not_ok', 1);
		}
		if (!$user->setLastName($last_name))
		{
			$bError = true;
			$tpl->assign('error_last_name_not_ok', 1);
		}

		if (!$user->setPassword($password))
		{
			$bError = true;
			$tpl->assign('error_password_not_ok', 1);
		}
		else if ($password != $password2)
		{
			$bError = true;
			$tpl->assign('error_password_diffs', 1);
		}

		if (!$user->setCountryCode(($country == 'XX') ? null : $country))
		{
			$bError = true;
			$tpl->assign('error_unkown', 1);
		}

		if ($tos != true)
		{
			$bError = true;
			$tpl->assign('error_tos_not_ok', 1);
		}

		if ($bError == false)
		{
			// try to register
			$user->setActivationCode($user->CreateCode());
			$user->setNode($opt['logic']['node']['id']);

			if ($user->save())
			{
				// send confirmation
				$user->sendRegistrationCode();

				//display confirmation
				$tpl->assign('confirm', 1);
			}
			else
			{
				$bReasonFound = false;
			
				// username or email already exists
				if ($user->existUsername($user->getUsername()))
				{
					$tpl->assign('error_username_exists', 1);
					$bReasonFound = true;
				}

				if ($user->existEMail($user->getEMail()))
				{
					$tpl->assign('error_email_exists', 1);
					$bReasonFound = true;
				}

				if ($bReasonFound == false)
					$tpl->assign('error_unkown', 1);
			}
		}
	}

	$rs = $countriesList->getRS(($country == 'XX') ? null : $country, $show_all_countries);
	$tpl->assign_rs('countries', $rs);
	sql_free_result($rs);

	if (!$countriesList->defaultUsed())
		$show_all_countries = 1;

	$tpl->assign('show_all_countries', $show_all_countries);
	$tpl->assign('country', $country);
	$tpl->assign('country_full', $countriesList->getCountryLocaleName($country));

  $tpl->assign('email', $email);
  $tpl->assign('first_name', $first_name);
  $tpl->assign('last_name', $last_name);
  $tpl->assign('username', $username);

	$tpl->display();
?>