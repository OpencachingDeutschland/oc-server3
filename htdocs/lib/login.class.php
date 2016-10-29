<?php
/***************************************************************************
 *    For license information see doc/license.txt
 *  Unicode Reminder メモ
 *  This class provides access to the login user data. Informations are
 *  stored in a cookie.
 *  Methods:
 *    verify()        validate the login-session
 *  Properties:
 *    userid          Integer 0 if no login, userid otherwise
 *    username        String username or ''
 *  !! See also lib2/login.class.php. !!
 ***************************************************************************/

define('LOGIN_UNKNOWN_ERROR', -1);     // unkown error occured
define('LOGIN_OK', 0);            // login succeeded
define('LOGIN_BADUSERPW', 1);     // bad username or password
define('LOGIN_TOOMUCHLOGINS', 2); // too many logins in short time
define('LOGIN_USERNOTACTIVE', 3); // the useraccount locked
define('LOGIN_EMPTY_USERPASSWORD', 4); // given username/password was empty
define('LOGIN_LOGOUT_OK', 5);          // logout was successfull

// login times in seconds
define('LOGIN_TIME', 60 * 60);
define('LOGIN_TIME_PERMANENT', 90 * 24 * 60 * 60);

require_once __DIR__ . '/../lib2/login.class.php';
$login = new login();
