<?php
/***************************************************************************
											./lang/de/ocstyle/varset.inc.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 template specific variables setup

 ****************************************************************************/

	//set all main template replacement to default values

	tpl_set_var('title', htmlspecialchars($pagetitle, ENT_COMPAT, 'UTF-8'));
	tpl_set_var('htmlheaders', '');
	tpl_set_var('lang', $lang);
	tpl_set_var('style', $style);
	tpl_set_var('loginbox', '&nbsp;');
	tpl_set_var('functionsbox', '<a href="index.php?page=suche">' . t('Search') . '</a> | <a href="index.php?page=sitemap">' . t('Sitemap') . '</a>');
	tpl_set_var('runtime', '');

	//set up main template specific string
	$sLoggedOut = '<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login" dir="ltr" style="display: inline;">' . t('User') . ':&nbsp;<input name="email" size="10" type="text" class="textboxes" value="" />&nbsp;' . t('Password') . ':&nbsp;<input name="password" size="10" type="password" class="textboxes" value="" />&nbsp;<input type="hidden" name="action" value="login" /><input type="hidden" name="target" value="{target}" /><input type="submit" name="LogMeIn" value="' . t('Login') . '" class="formbuttons" style="width: 60px;" /></form>';
	$sLoggedIn = t('Login as') . ' <a href="myhome.php">{username}</a> - <a href="login.php?action=logout">' . t('Logout') . '</a>';

	// target in Loginbox setzen
	$target = basename($_SERVER['PHP_SELF']).'?';

	// REQUEST-Variablen durchlaufen und an target anhaengen
	$allowed = array('cacheid', 'userid', 'logid', 'desclang', 'descid');
	reset ($_REQUEST);
	while (list ($varname, $varvalue) = each ($_REQUEST))
	{
		if (in_array($varname, $allowed))
		{
			$target .= $varname.'='.$varvalue.'&';
		}
	}
	if (mb_substr($target, -1) == '?' || mb_substr($target, -1) == '&') $target = mb_substr($target, 0, -1);
	$sLoggedOut = mb_ereg_replace('{target}', $target, $sLoggedOut);

	$functionsbox_start_tag = '';
	$functionsbox_middle_tag = ' | ';
	$functionsbox_end_tag = '';

	$tpl_subtitle = '';

	//other vars
	$login_required = t('Sorry, the requested operation cannot be performed because you are not logged in with an user account.');

	$dberrormsg = t('A database command could not be performed.');

	$error_prefix = '<span class="errormsg">';
	$error_suffix = '</span>';
?>