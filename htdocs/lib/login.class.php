<?php
/***************************************************************************
 *	For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  This class provides access to the login user data. Informations are
 *  stored in a cookie. Authentication has 2 levels unverified and verified.
 *
 *  Unverified means: In the cookie is a userid and username provided, but
 *                    the system didn't checked if that information is valid.
 *                    This is good enough, if the login information is only
 *                    used to display e.g. the loginbox. There is no 
 *                    security-hole if someone cheats the cookie.
 *
 *  Verified means:   In the cookie is a userid and username provided and
 *                    the system checkd the information. A valid login-
 *                    session exists. You have to verify the login-session
 *                    when you read personal informations or write
 *                    logentries, caches etc. to the database.
 *
 *  Methods:
 *    verify()        validate the login-session
 *    try_login()     try to login with the given user/password
 *    logout()        logout the user
 *
 *  Properties:
 *    userid          Integer 0 if no login, userid otherwise
 *    username        String username or ''
 *
 ***************************************************************************/

	define('LOGIN_OK', 0);            // login succeeded
	define('LOGIN_BADUSERPW', 1);     // bad username or password
	define('LOGIN_TOOMUCHLOGINS', 2); // too many logins in short time
	define('LOGIN_USERNOTACTIVE', 3); // the useraccount locked

	// login times in seconds
	define('LOGIN_TIME', 60*60);
	define('LOGIN_TIME_PERMANENT', 90*24*60*60);

	$login = new login();

class login
{
	var $userid = 0;
	var $username = '';
	var $lastlogin = 0;
	var $permanent = false;
	var $sessionid = '';
	var $verified = false;
	var $admin = false;

	function login()
	{
		global $cookie;

		if ($cookie->is_set('userid') && $cookie->is_set('username'))
		{
			$this->userid = $cookie->get('userid')+0;
			$this->username = $cookie->get('username');
			$this->permanent = (($cookie->get('permanent')+0) == 1);
			$this->lastlogin = $cookie->get('lastlogin');
			$this->sessionid = $cookie->get('sessionid');
			$this->admin = (($cookie->get('admin')+0) == 1);
			$this->verified = false;

			// wenn lastlogin zu 50% abgelaufen, verify()
			// permanent = 90 Tage, sonst 60 Minuten
			if ((($this->permanent == true) && (strtotime($this->lastlogin) + LOGIN_TIME/2 < time())) ||
			    (($this->permanent == false) && (strtotime($this->lastlogin) + LOGIN_TIME_PERMANENT/2 < time())))
				$this->verify();

			if ($this->admin != false)
				$this->verify();
		}
		else
			$this->pClear();
	}

	function pClear()
	{
		// set to no valid login
		$this->userid = 0;
		$this->username = '';
		$this->permanent = false;
		$this->lastlogin = '';
		$this->sessionid = '';
		$this->admin = false;
		$this->verified = true;

		$this->pStoreCookie();
	}
	
	function pStoreCookie()
	{
		global $cookie;
		$cookie->set('userid', $this->userid);
		$cookie->set('username', $this->username);
		$cookie->set('permanent', ($this->permanent==true ? 1 : 0));
		$cookie->set('lastlogin', $this->lastlogin);
		$cookie->set('sessionid', $this->sessionid);
		$cookie->set('admin', ($this->admin==true ? 1 : 0));
	}

	function verify()
	{
		if ($this->verified == true)
			return;

		if ($this->userid == 0)
		{
			$this->pClear();
			return;
		}

		$min_lastlogin = date('Y-m-d H:i:s', time() - LOGIN_TIME);
		$min_lastlogin_permanent = date('Y-m-d H:i:s', time() - LOGIN_TIME_PERMANENT);

		$rs = sql("SELECT `sys_sessions`.`last_login`, `user`.`admin` FROM &db.`sys_sessions`, &db.`user` WHERE `sys_sessions`.`user_id`=`user`.`user_id` AND `user`.`is_active_flag`=1 AND `sys_sessions`.`uuid`='&1' AND `sys_sessions`.`user_id`='&2' AND ((`sys_sessions`.`permanent`=1 AND `sys_sessions`.`last_login`>'&3') OR (`sys_sessions`.`permanent`=0 AND `sys_sessions`.`last_login`>'&4'))", $this->sessionid, $this->userid, $min_lastlogin_permanent, $min_lastlogin);
		if ($rUser = sql_fetch_assoc($rs))
		{
			if ((($this->permanent == true) && (strtotime($rUser['last_login']) + LOGIN_TIME/2 < time())) ||
			    (($this->permanent == false) && (strtotime($rUser['last_login']) + LOGIN_TIME_PERMANENT/2 < time())))
			{
				sql("UPDATE `sys_sessions` SET `sys_sessions`.`last_login`=NOW() WHERE `sys_sessions`.`uuid`='&1' AND `sys_sessions`.`user_id`='&2'", $this->sessionid, $this->userid);
				$rUser['last_login'] = date('Y-m-d H:i:s');
			}

			// user.last_login is used for statics, so we keep it up2date
			sql("UPDATE `user` SET `user`.`last_login`=NOW() WHERE `user`.`user_id`='&1'", $this->userid);

			$this->lastlogin = $rUser['last_login'];
			$this->admin = ($rUser['admin'] == 1);
			$this->verified = true;
		}
		else
		{
			// prevent bruteforce
			sql("INSERT INTO `sys_logins` (`remote_addr`, `success`) VALUES ('&1', 0)", $_SERVER['REMOTE_ADDR']);

			$this->pClear();
		}
		sql_free_result($rs);

		$this->pStoreCookie();
		return;
	}

	function try_login($user, $password, $permanent)
	{
		global $opt;

		$this->pClear();

		// check the number of logins in the last hour ...
		sql("DELETE FROM `sys_logins` WHERE `timestamp`<'&1'", date('Y-m-d H:i:s', time() - 3600));
		$logins_count = sqlValue("SELECT COUNT(*) `count` FROM `sys_logins` WHERE `remote_addr`='" . sql_escape($_SERVER['REMOTE_ADDR']) . "'", 0);
		if ($logins_count > 24)
			return LOGIN_TOOMUCHLOGINS;

		// delete old sessions
		$min_lastlogin_permanent = date('Y-m-d H:i:s', time() - LOGIN_TIME_PERMANENT);
		sql("DELETE FROM `sys_sessions` WHERE `last_login`<'&1'", $min_lastlogin_permanent);

		$pwmd5 = md5($password);
		if ($opt['login']['hash'])
			$pwmd5 = hash('sha512', $pwmd5);

		// compare $user with email and username, if both matches use email
		$rsUser = sql("SELECT `user_id`, `username`, 2 AS `prio`, `is_active_flag`, `permanent_login_flag`, `admin` FROM `user` WHERE `username`='&1' AND `password`='&2' UNION
		               SELECT `user_id`, `username`, 1 AS `prio`, `is_active_flag`, `permanent_login_flag`, `admin` FROM `user` WHERE `email`='&1' AND `password`='&2' ORDER BY `prio` ASC LIMIT 1", $user, $pwmd5);
		$rUser = sql_fetch_assoc($rsUser);
		sql_free_result($rsUser);

		if ($permanent == null)
			$permanent = ($rUser['permanent_login_flag'] == 1);

		if ($rUser)
		{
			// ok, there is a valid login
			if ($rUser['is_active_flag'] != 0)
			{
				// begin session
				$uuid = sqlValue('SELECT UUID()', '');
				sql("INSERT INTO `sys_sessions` (`uuid`, `user_id`, `permanent`, `last_login`) VALUES ('&1', '&2', '&3', NOW())", $uuid, $rUser['user_id'], ($permanent!=false ? 1 : 0));
				$this->userid = $rUser['user_id'];
				$this->username = $rUser['username'];
				$this->permanent = $permanent;
				$this->lastlogin = date('Y-m-d H:i:s');
				$this->sessionid = $uuid;
				$this->admin = ($rUser['admin'] == 1);
				$this->verified = true;

				$retval = LOGIN_OK;
			}
			else
				$retval = LOGIN_USERNOTACTIVE;
		}
		else
		{
			// sorry, bad login
			$retval = LOGIN_BADUSERPW;
		}

		sql("INSERT INTO `sys_logins` (`remote_addr`, `success`, `timestamp`) VALUES ('&1', '&2', NOW())", $_SERVER['REMOTE_ADDR'], ($rUser===false ? 0 : 1));

		// store to cookie
		$this->pStoreCookie();

		return $retval;
	}

	function logout()
	{
		sql("DELETE FROM `sys_sessions` WHERE `uuid`='&1' AND `user_id`='&2'", $this->sessionid, $this->userid);
		$this->pClear();
	}

	public function hasAdminPriv($privilege = false)
	{
		global $cookie;

		$this->verify();

		if ($privilege === false)
			return $this->admin != 0;

		return ($this->admin & $privilege) == $privilege;
	}
}
?>