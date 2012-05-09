<?php
	/***************************************************************************
																./lib/auth.inc.php
																--------------------
			begin                : Fri September 16 2005
			copyright            : (C) 2005 The OpenCaching Group
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

		all login/logout related functions
		Dont include this file by hand - it will be included from common.inc.php

	****************************************************************************/

	require($opt['rootpath'] . 'lib/login.class.php');

	$autherr = 0;
	define('AUTHERR_NOERROR', 0);
	define('AUTHERR_TOOMUCHLOGINS', 1);
	define('AUTHERR_INVALIDEMAIL', 2);
	define('AUTHERR_WRONGAUTHINFO', 3);
	define('AUTHERR_USERNOTACTIVE', 4);

	/* auth_UsernameFromID - get the username from the given id,
	 * otherwise false
	 */
	function auth_UsernameFromID($userid)
	{
		//select the right user
		$rs = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $userid);
		if (mysql_num_rows($rs) > 0)
		{
			$record = sql_fetch_array($rs);
			return $record['username'];
		}
		else
		{
			//user not exists
			return false;
		}
	}

	/* auth_user - fills usr[]
	 * no return value
	 */
	function auth_user()
	{
		global $usr, $login;
		$login->verify();

		if ($login->userid != 0)
		{
			//set up $usr array
			$usr['userid'] = $login->userid;
			$usr['email'] = sqlValue("SELECT `email` FROM `user` WHERE `user_id`='" . sql_escape($login->userid) .  "'", '');
			$usr['username'] = $login->username;
		}
		else
			$usr = false;

		return;
	}

	/* auth_login - try to log in a user
	 * returns the userid on success, otherwise false
	 */
	function auth_login($user, $password)
	{
		global $login, $autherr;
		$retval = $login->try_login($user, $password, null);

		switch ($retval)
		{
			case LOGIN_TOOMUCHLOGINS:
				$autherr = AUTHERR_TOOMUCHLOGINS;
				return false;

			case LOGIN_USERNOTACTIVE:
				$autherr = AUTHERR_USERNOTACTIVE;
				return false;

			case LOGIN_BADUSERPW:
				$autherr = AUTHERR_WRONGAUTHINFO;
				return false;

			case LOGIN_OK:
				$autherr = AUTHERR_NOERROR;
				return $login->userid;
			
			default:
				$autherr = AUTHERR_WRONGAUTHINFO;
				return false;
		}
	}

	/* auth_logout - log out the user
		* returns false if the user wasn't logged in, true if success
		*/
	function auth_logout()
	{
		global $login, $usr;
		if ($login->userid != 0)
		{
			$login->logout();
			return true;
		}
		else
		{
			$usr = false;
			return false;
		}
	}
?>