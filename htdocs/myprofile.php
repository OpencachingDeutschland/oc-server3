<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require_once('./lib2/web.inc.php');
	require_once('./lib2/logic/user.class.php');
	require_once('./lib2/logic/coordinate.class.php');
	require_once('./lib2/logic/countriesList.class.php');

	$tpl->name = 'myprofile';
	$tpl->menuitem = MNU_MYPROFILE_DATA;

	$login->verify();

	$action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : 'view';
	if ($action != 'change' &&  $action != 'changeemail' && $action != 'view')
		$action = 'view';

	if ($login->userid == 0)
	{
		if ($action == 'change' || $action == 'changeemail')
			$tpl->redirect('login.php?target=' . urlencode('myprofile.php?action=change'));
		else
			$tpl->redirect('login.php?target=myprofile.php');
	}

	if ($action == 'changeemail')
		$tpl->redirect('newemail.php');
	else if ($action == 'change')
		change();
	else
		display();

exit;

function change()
{
	global $tpl, $login;

	if (isset($_REQUEST['cancel']))
		$tpl->redirect('myprofile.php');

	$user = new user($login->userid);
	assignFromUser($user);

	$bError = false;

	// set user properties
	if (isset($_REQUEST['username']))
	{
		$tpl->assign('username', $_REQUEST['username']);
		if (!$user->setUsername($_REQUEST['username']))
		{
			$tpl->assign('usernameErrorInvalidChars', true);
			$bError = true;
		}
	}

	if (isset($_REQUEST['firstName']))
	{
		$tpl->assign('firstName', $_REQUEST['firstName']);
		if (!$user->setFirstName($_REQUEST['firstName']))
		{
			$tpl->assign('firstNameError', true);
			$bError = true;
		}
	}

	if (isset($_REQUEST['lastName']))
	{
		$tpl->assign('lastName', $_REQUEST['lastName']);
		if (!$user->setLastName($_REQUEST['lastName']))
		{
			$tpl->assign('lastNameError', true);
			$bError = true;
		}
	}

	if (isset($_REQUEST['country']))
	{
		$tpl->assign('countryCode', $_REQUEST['country']);
		if (!$user->setCountryCode(($_REQUEST['country']=='XX') ? null : $_REQUEST['country']))
		{
			$tpl->assign('countryError', true);
			$bError = true;
		}
	}

	if (isset($_REQUEST['notifyRadius']))
	{
		$tpl->assign('notifyRadius', $_REQUEST['notifyRadius']+0);
		if (!$user->setNotifyRadius($_REQUEST['notifyRadius']+0))
		{
			$tpl->assign('notifyRadiusError', true);
			$bError = true;
		}
	}

	if (isset($_REQUEST['notifyOconly']))
	{
		$tpl->assign('notifyOconly', $_REQUEST['notifyOconly']+0);
		$user->setNotifyOconly($_REQUEST['notifyOconly'] != 0);
	}
	else if (isset($_REQUEST['save']))
		$user->setNotifyOconly(false);

	$oconly_helplink = helppagelink('oconly');
	$tpl->assign('oconly_helpstart', $oconly_helplink);
	$tpl->assign('oconly_helpend', $oconly_helplink != '' ? '</a>' : '');

	$coord['lat'] = coordinate::parseRequestLat('coord');
	$coord['lon'] = coordinate::parseRequestLon('coord');
	if (($coord['lat'] !== false) && ($coord['lon'] !== false))
	{
		$tpl->assign('coordsDecimal', $coord);
		if (!$user->setLatitude($coord['lat']))
		{
			$tpl->assign('latitudeError', true);
			$bError = true;
		}
		if (!$user->setLongitude($coord['lon']))
		{
			$tpl->assign('longitudeError', true);
			$bError = true;
		}
	}

	$bAccMailing = isset($_REQUEST['save']) ? isset($_REQUEST['accMailing']) : $user->getAccMailing();
	$tpl->assign('accMailing', $bAccMailing);
	$user->setAccMailing($bAccMailing);

	$bUsePMR = isset($_REQUEST['save']) ? isset($_REQUEST['usePMR']) : $user->getUsePMR();
	$tpl->assign('usePMR', $bUsePMR);
	$user->setUsePMR($bUsePMR);

	$bPermanentLogin = isset($_REQUEST['save']) ? isset($_REQUEST['permanentLogin']) : $user->getPermanentLogin();
	$tpl->assign('permanentLogin', $bPermanentLogin);
	$user->setPermanentLogin($bPermanentLogin);

	$bNoWysiwygEditor = isset($_REQUEST['save']) ? isset($_REQUEST['noWysiwygEditor']) : $user->getNoWysiwygEditor();
	$tpl->assign('noWysiwygEditor', $bNoWysiwygEditor);
	$user->setNoWysiwygEditor($bNoWysiwygEditor);

	$bUsermailSendAddress = isset($_REQUEST['save']) ? isset($_REQUEST['sendUsermailAddress']) : $user->getUsermailSendAddress();
	$tpl->assign('sendUsermailAddress', $bUsermailSendAddress);
	$user->setUsermailSendAddress($bUsermailSendAddress);

	if (!$bError && isset($_REQUEST['save']))
	{
		if ($user->getAnyChanged())
		{
			if (!$user->save())
			{
				$bError = true;

				// check for duplicate username
				if ($user->getUsernameChanged() && user::existUsername($_REQUEST['username']))
					$tpl->assign('errorUsernameExist', true);
				else
					$tpl->assign('errorUnknown', true);
			}
			else
				$tpl->redirect('myprofile.php');
		}
		else
			$tpl->redirect('myprofile.php');
	}

	$showAllCountries = isset($_REQUEST['showAllCountries']) ? $_REQUEST['showAllCountries']+0 : 0;
	if (isset($_REQUEST['showAllCountriesSubmit']))
		$showAllCountries = 1;

	$countriesList = new countriesList();
	$rs = $countriesList->getRS($user->getCountryCode(), $showAllCountries!=0);
	$tpl->assign_rs('countries', $rs);
	sql_free_result($rs);
	if ($countriesList->defaultUsed() == true)
		$showAllCountries = 0;
	else
		$showAllCountries = 1;
	$tpl->assign('showAllCountries', $showAllCountries);

	$tpl->assign('edit', true);
	$tpl->display();
}

function display()
{
	global $tpl, $login;

	$user = new user($login->userid);
	assignFromUser($user);

	$tpl->display();
}

function assignFromUser($user)
{
	global $tpl;

	$tpl->assign('username', $user->getUsername());
	$tpl->assign('email', $user->getEMail());
	$tpl->assign('firstName', $user->getFirstName());
	$tpl->assign('lastName', $user->getLastName());
	$tpl->assign('country', $user->getCountry());
	$tpl->assign('countryCode', $user->getCountryCode());

	$coords = new coordinate($user->getLatitude(), $user->getLongitude());
	$tpl->assign('coords', $coords->getDecimalMinutes());
	$tpl->assign('coordsDecimal', $coords->getFloat());

	$tpl->assign('notifyRadius', $user->getNotifyRadius());

	$tpl->assign('notifyOconly', $user->getNotifyOconly());
	$oconly_helplink = helppagelink('oconly');
	$tpl->assign('oconly_helpstart', $oconly_helplink);
	$tpl->assign('oconly_helpend', $oconly_helplink != '' ? '</a>' : '');

	$tpl->assign('registeredSince', $user->getDateRegistered());

	$tpl->assign('accMailing', $user->getAccMailing());

	$tpl->assign('usePMR', $user->getUsePMR());
	$tpl->assign('permanentLogin', $user->getPermanentLogin());
	$tpl->assign('noWysiwygEditor', $user->getNoWysiwygEditor());
	$tpl->assign('sendUsermailAddress', $user->getUsermailSendAddress());
}

?>
