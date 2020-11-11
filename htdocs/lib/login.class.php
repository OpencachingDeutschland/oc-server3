<?php
/***************************************************************************
 * For license information see LICENSE.md
 * see lib2/login.class.php. !!
 ***************************************************************************/

// unknown error occurred
define('LOGIN_UNKNOWN_ERROR', -1);
// login succeeded
define('LOGIN_OK', 0);
// bad username or password
define('LOGIN_BADUSERPW', 1);
// too many login attempts in short time
define('LOGIN_TOOMUCHLOGINS', 2);
// the user account locked
define('LOGIN_USERNOTACTIVE', 3);
// given username/password was empty
define('LOGIN_EMPTY_USERPASSWORD', 4);
// logout was successfully
define('LOGIN_LOGOUT_OK', 5);

// login times in seconds
define('LOGIN_TIME', 60 * 60);
define('LOGIN_TIME_PERMANENT', 90 * 24 * 60 * 60);

$login = new login();

class login
{
    public $userid = 0;
    public $username = '';
    public $permanent = false;
    public $lastlogin = '';
    public $sessionid = '';
    public $admin = 0;
    public $verified = false;

    public function __construct()
    {
        global $cookie;

        if ($cookie->is_set('userid') && $cookie->is_set('username')) {
            $this->userid = $cookie->get('userid') + 0;
            $this->username = $cookie->get('username');
            $this->permanent = (($cookie->get('permanent') + 0) == 1);
            $this->lastlogin = $cookie->get('lastlogin');
            $this->sessionid = $cookie->get('sessionid');
            $this->verified = false;

            $this->verify();
        } else {
            $this->pClear();
        }
    }

    public function pClear(): void
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

    public function pStoreCookie(): void
    {
        global $cookie;
        $cookie->set('userid', $this->userid);
        $cookie->set('username', $this->username);
        $cookie->set('permanent', ($this->permanent == true ? 1 : 0));
        $cookie->set('lastlogin', $this->lastlogin);
        $cookie->set('sessionid', $this->sessionid);
    }

    public function verify(): void
    {
        global $locale, $opt;

        if ($this->verified == true) {
            return;
        }

        if ($this->userid == 0) {
            $this->pClear();

            return;
        }

        if ($this->checkLoginsCount() == false) {
            $this->pClear();

            return;
        }

        $min_lastlogin = date('Y-m-d H:i:s', time() - LOGIN_TIME);
        $min_lastlogin_permanent = date('Y-m-d H:i:s', time() - LOGIN_TIME_PERMANENT);

        $rs = sql(
            "SELECT `sys_sessions`.`last_login`, `user`.`admin`
             FROM &db.`sys_sessions`, &db.`user`
             WHERE `sys_sessions`.`user_id`=`user`.`user_id`
             AND `user`.`is_active_flag`=1
             AND `sys_sessions`.`uuid`='&1'
             AND `sys_sessions`.`user_id`='&2'
             AND ((`sys_sessions`.`permanent`=1
             AND `sys_sessions`.`last_login`>'&3')
             OR (`sys_sessions`.`permanent`=0 AND `sys_sessions`.`last_login`>'&4'))
             ",
            $this->sessionid,
            $this->userid,
            $min_lastlogin_permanent,
            $min_lastlogin
        );

        // sys_session.last_login controls the automatic logout of users at the OC website.
        // user.last_login gives the overall last login date, including OKAPI logins.

        if ($rUser = sql_fetch_assoc($rs)) {
            if ((($this->permanent == true) && (strtotime($rUser['last_login']) + LOGIN_TIME_PERMANENT  / 2 < time())) ||
                (($this->permanent == false) && (strtotime($rUser['last_login']) + LOGIN_TIME / 2 < time()))
            ) {
                sql(
                    "UPDATE `sys_sessions`
                     SET `sys_sessions`.`last_login`=NOW()
                     WHERE `sys_sessions`.`uuid`='&1'
                     AND `sys_sessions`.`user_id`='&2'",
                    $this->sessionid,
                    $this->userid
                );
                $rUser['last_login'] = date('Y-m-d H:i:s');
            }

            if (isset($locale)) {
                sql(
                    "UPDATE `user`
                     SET `last_login`=NOW(),
                         `language`='&2',
                         `language_guessed` = 0,
                         `domain`='&3'
                     WHERE `user_id`='&1'",
                    $this->userid,
                    $locale,
                    $opt['page']['domain']
                );
            } else {
                sql("UPDATE `user` SET `last_login`=NOW() WHERE `user_id`='&1'", $this->userid);
            }

            $this->lastlogin = $rUser['last_login'];
            $this->admin = $rUser['admin'];
            $this->verified = true;
        } else {
            // prevent bruteforce
            sql("INSERT INTO `sys_logins` (`remote_addr`, `success`) VALUES ('&1', 0)", $_SERVER['REMOTE_ADDR']);

            $this->pClear();
        }
        sql_free_result($rs);

        $this->pStoreCookie();
    }

    /**
     * @param int|bool $privilege
     * @return bool
     */
    public function hasAdminPriv($privilege = false)
    {
        $this->verify();

        if ($privilege === false) {
            return $this->admin > 0;
        }

        return ($this->admin & $privilege) == $privilege;
    }

    public function listingAdmin()
    {
        global $opt;

        return $this->hasAdminPriv(ADMIN_LISTING) && $opt['logic']['admin']['enable_listing_admins'];
    }

    public function checkLoginsCount()
    {
        global $opt;

        // cleanup old entries
        // (execute only every 50 search calls)
        if (mt_rand(1, 50) === 1) {
            sql("DELETE FROM `sys_logins` WHERE `date_created`<'&1'", date('Y-m-d H:i:s', time() - 3600));
        }

        // check the number of login attempts in the last hour ...
        $loginCount = sqlValue(
            "SELECT COUNT(*) `count` FROM `sys_logins` WHERE `remote_addr`='" . sql_escape(
                $_SERVER['REMOTE_ADDR']
            ) . "' AND `date_created`>'" . sql_escape(date('Y-m-d H:i:s', time() - 3600)) . "'",
            0
        );
        if ($loginCount > $opt['page']['max_logins_per_hour']) {
            return false;
        }

        return true;
    }
}
