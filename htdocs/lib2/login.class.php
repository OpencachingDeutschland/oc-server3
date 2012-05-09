<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  This class provides access to the login user data. Informations are
 *  stored in a cookie.
 *
 *  Methods:
 *    verify()        validate the login-session (automatically invoked)
 *    try_login()     try to login with the given user/password
 *    logout()        logout the user
 *
 *  Properties:
 *    userid          Integer 0 if no login, userid otherwise
 *    username        String username or ''
 *
 ***************************************************************************/

	define('LOGIN_UNKNOWN_ERROR', -1);     // unkown error occured
	define('LOGIN_OK', 0);                 // login succeeded
	define('LOGIN_BADUSERPW', 1);          // bad username or password
	define('LOGIN_TOOMUCHLOGINS', 2);      // too many logins in short time
	define('LOGIN_USERNOTACTIVE', 3);      // the useraccount locked
	define('LOGIN_EMPTY_USERPASSWORD', 4); // given username/password was empty
	define('LOGIN_LOGOUT_OK', 5);          // logout was successfull

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
	var $admin = 0;

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
			$this->admin = $cookie->get('admin')+0;
			$this->verified = false;

			$this->verify();
		}
		else
			$this->pClear();
	}

	// return true on success
	function restoreSession($sid)
	{
		$min_lastlogin = date('Y-m-d H:i:s', time() - LOGIN_TIME);

		if ($this->checkLoginsCount() == false)
		{
			$this->pClear();
			return false;
		}

		$rs = sqlf("SELECT `sys_sessions`.`uuid` `sid`, `user`.`user_id`, `sys_sessions`.`last_login`, `user`.`admin`, `user`.`username` FROM &db.`sys_sessions`, &db.`user` WHERE `sys_sessions`.`user_id`=`user`.`user_id` AND `user`.`is_active_flag`=1 AND `sys_sessions`.`uuid`='&1' AND `sys_sessions`.`permanent`=0 AND `sys_sessions`.`last_login`>'&2'", $sid, $min_lastlogin);
		$r = sql_fetch_assoc($rs);
		sql_free_result($rs);

		if ($r)
		{
			sqlf("UPDATE `sys_sessions` SET `sys_sessions`.`last_login`=NOW() WHERE `sys_sessions`.`uuid`='&1' AND `sys_sessions`.`user_id`='&2'", $r['sid'], $r['user_id']);
			sqlf("UPDATE `user` SET `user`.`last_login`=NOW() WHERE `user`.`user_id`='&1'", $r['user_id']);

			$this->userid = $r['user_id'];
			$this->username = $r['username'];
			$this->permanent = false;
			$this->lastlogin = $r['last_login'];
			$this->sessionid = $r['sid'];
			$this->admin = $r['admin'];
			$this->verified = true;

			return true;
		}
		else
		{
			// prevent bruteforce
			sql("INSERT INTO `sys_logins` (`remote_addr`, `success`) VALUES ('&1', 0)", $_SERVER['REMOTE_ADDR']);
			return false;
		}
	}

	function pClear()
	{
		// set to no valid login
		$this->userid = 0;
		$this->username = '';
		$this->permanent = false;
		$this->lastlogin = '';
		$this->sessionid = '';
		$this->admin = 0;
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
		$cookie->set('admin', $this->admin);
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

		if ($this->checkLoginsCount() == false)
		{
			$this->pClear();
			return;
		}

		$min_lastlogin = date('Y-m-d H:i:s', time() - LOGIN_TIME);
		$min_lastlogin_permanent = date('Y-m-d H:i:s', time() - LOGIN_TIME_PERMANENT);

		$rs = sqlf("SELECT `sys_sessions`.`last_login`, `user`.`admin`, `user`.`username` FROM &db.`sys_sessions`, &db.`user` WHERE `sys_sessions`.`user_id`=`user`.`user_id` AND `user`.`is_active_flag`=1 AND `sys_sessions`.`uuid`='&1' AND `sys_sessions`.`user_id`='&2' AND ((`sys_sessions`.`permanent`=1 AND `sys_sessions`.`last_login`>'&3') OR (`sys_sessions`.`permanent`=0 AND `sys_sessions`.`last_login`>'&4'))", $this->sessionid, $this->userid, $min_lastlogin_permanent, $min_lastlogin);
		if ($rUser = sql_fetch_assoc($rs))
		{
			if ((($this->permanent == true) && (strtotime($rUser['last_login']) + LOGIN_TIME/2 < time())) ||
			    (($this->permanent == false) && (strtotime($rUser['last_login']) + LOGIN_TIME_PERMANENT/2 < time())))
			{
				sqlf("UPDATE `sys_sessions` SET `sys_sessions`.`last_login`=NOW() WHERE `sys_sessions`.`uuid`='&1' AND `sys_sessions`.`user_id`='&2'", $this->sessionid, $this->userid);
				$rUser['last_login'] = date('Y-m-d H:i:s');
			}

			// user.last_login is used for statics, so we keep it up2date
			sqlf("UPDATE `user` SET `user`.`last_login`=NOW() WHERE `user`.`user_id`='&1'", $this->userid);

			$this->lastlogin = $rUser['last_login'];
			$this->username = $rUser['username'];
			$this->admin = $rUser['admin'];
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

		if ($password == '')
			return LOGIN_EMPTY_USERPASSWORD;

		$pwmd5 = md5($password);
		if ($opt['logic']['password_hash'])
			$pwmd5 = hash('sha512', $pwmd5);

		return $this->try_login_md5($user, $pwmd5, $permanent);
	}

	function checkLoginsCount()
	{
		global $opt;

		// cleanup old entries
		// (execute only every 50 search calls)
		if (rand(1, 50) == 1)
			sqlf("DELETE FROM `sys_logins` WHERE `date_created`<'&1'", date('Y-m-d H:i:s', time() - 3600));

		// check the number of logins in the last hour ...
		$logins_count = sqlf_value("SELECT COUNT(*) `count` FROM `sys_logins` WHERE `remote_addr`='&1' AND `date_created`>'&2'", 0, $_SERVER['REMOTE_ADDR'], date('Y-m-d H:i:s', time() - 3600));
		if ($logins_count > $opt['page']['max_logins_per_hour'])
			return false;
		else
			return true;
	}

	function try_login_md5($user, $pwmd5, $permanent)
	{
		global $opt;
		$this->pClear();

		if ($user == '' || $pwmd5 == '')
			return LOGIN_EMPTY_USERPASSWORD;

		if ($this->checkLoginsCount() == false)
			return LOGIN_TOOMUCHLOGINS;

		// delete old sessions
		$min_lastlogin_permanent = date('Y-m-d H:i:s', time() - LOGIN_TIME_PERMANENT);
		sqlf("DELETE FROM `sys_sessions` WHERE `last_login`<'&1'", $min_lastlogin_permanent);

		// compare $user with email and username, if both matches use email
		$rsUser = sqlf("SELECT `user_id`, `username`, 2 AS `prio`, `is_active_flag`, `permanent_login_flag`, `admin` FROM `user` WHERE `username`='&1' AND `password`='&2' UNION
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
				$uuid = sqlf_value('SELECT UUID()', '');
				sqlf("INSERT INTO `sys_sessions` (`uuid`, `user_id`, `permanent`, `last_login`) VALUES ('&1', '&2', '&3', NOW())", $uuid, $rUser['user_id'], ($permanent!=false ? 1 : 0));
				$this->userid = $rUser['user_id'];
				$this->username = $rUser['username'];
				$this->permanent = $permanent;
				$this->lastlogin = date('Y-m-d H:i:s');
				$this->sessionid = $uuid;
				$this->admin = $rUser['admin'];
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

		sqlf("INSERT INTO `sys_logins` (`remote_addr`, `success`) VALUES ('&1', '&2')", $_SERVER['REMOTE_ADDR'], ($rUser===false ? 0 : 1));

		// store to cookie
		$this->pStoreCookie();

		return $retval;
	}

	function getUserCountry()
	{
		global $opt, $cookie;

		// language specified in cookie?
		if ($cookie->is_set('usercountry'))
		{
			$sCountry = $cookie->get('usercountry', null);
			if ($sCountry != null)
				return $sCountry;
		}

		// user specified a language?
		if ($this->userid != 0)
		{
			$sCountry = sql_value("SELECT `country` FROM `user` WHERE `user_id`='&1'", null, $this->userid);
			if ($sCountry != null)
				return $sCountry;
		}

		// default country of this language
		if (isset($opt['locale'][$opt['template']['locale']]['country']))
			return $opt['locale'][$opt['template']['locale']]['country'];

		// default country of installation (or domain)
		return $opt['template']['default']['country'];
	}

	function logout()
	{
		if ($this->userid != 0)
			sqlf("DELETE FROM `sys_sessions` WHERE `uuid`='&1' AND `user_id`='&2'", $this->sessionid, $this->userid);

		$this->pClear();
	}

	public function hasAdminPriv($privilege = false)
	{
		if ($privilege === false)
			return $this->admin != 0;

		return ($this->admin & $privilege) == $privilege;
	}
}
?>
