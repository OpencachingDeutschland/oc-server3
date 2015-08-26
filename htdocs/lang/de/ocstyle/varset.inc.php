<?php
/****************************************************************************
											./lang/de/ocstyle/varset.inc.php
															-------------------
		begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

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
	$sLoggedOut = '<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login" dir="ltr" style="display: inline;"><b>' . t('User:') . '</b>&nbsp;&nbsp;<input name="email" size="10" type="text" class="textboxes" value="" />&nbsp;&nbsp;&nbsp;<b>' . t('Password:') . '</b>&nbsp;&nbsp;<input name="password" size="10" type="password" class="textboxes" value="" />&nbsp;<input type="hidden" name="action" value="login" /><input type="hidden" name="target" value="{target}" /><input type="hidden" name="source" value="titlebar" />&nbsp;<input type="submit" name="LogMeIn" value="' . t('Login') . '" class="formbutton" style="width: 74px;" onclick="submitbutton(\'LogMeIn\')" /></form>';
	$sLoggedIn = "<b>" . t('Logged in as') . ' <a href="myhome.php">{username}</a></b> - <a href="login.php?action=logout">' . t('Logout') . '</a>';

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
	$login_required = t('Please login to continue:');

	$dberrormsg = t('A database command could not be performed.');

	$error_prefix = '<span class="errormsg">';
	$error_suffix = '</span>';
?>