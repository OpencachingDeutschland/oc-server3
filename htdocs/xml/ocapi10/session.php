<?php
/*****************************************************************************************
 *  Opencaching Webservice API Version 1.0
 *
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Session management
 *
 *   The session-id identifies the login session for subsequent function calls
 *
 *  CAUTION:
 *
 *   If an hacker can sniff traffic from and to the Opencaching.de Server it is
 *   possible to make an offline brute force attack on the users password.
 *
 *   The entire security relies on the users password strength!
 *
 *  Function list:
 *
 *   string Login($user, $pwMd5)
 *          $user        := username or email
 *          $pwMd5       := MD5 of the users password
 *          return value := sessionid
 *
 *          When login does not succeed, an SOAP exception is raised.
 *
 *   bool IsValidSession($sessionid)
 *        $sessionid   := sessionid returned by Login
 *        return value := true when sessionid is value, else false
 *
 *   void Logout($sessionid)
 *        $sessionid   := sessionid returned by Login
 *
 ******************************************************************************************/

	// Empty user or password supplied
	define('WS_ERR_LOGIN_EMPTY_USERPASSWORD_ID', 1000); 
	define('WS_ERR_LOGIN_EMPTY_USERPASSWORD_STR', 'WS_ERR_LOGIN_EMPTY_USERPASSWORD');

	// Too much logins in last hour
	define('WS_ERR_LOGIN_TOOMUCHLOGINS_ID', 1001);
	define('WS_ERR_LOGIN_TOOMUCHLOGINS_STR', 'WS_ERR_LOGIN_TOOMUCHLOGINS');

	// The user has been deactivated
	define('WS_ERR_LOGIN_USERNOTACTIVE_ID', 1002);
	define('WS_ERR_LOGIN_USERNOTACTIVE_STR', 'WS_ERR_LOGIN_USERNOTACTIVE');

	// Username or password does not match
	define('WS_ERR_LOGIN_BADUSERPW_ID', 1003);
	define('WS_ERR_LOGIN_BADUSERPW_STR', 'WS_ERR_LOGIN_BADUSERPW');

	$opt['rootpath'] = '../../';
	require_once($opt['rootpath'] . 'lib2/nusoap.inc.php');

	initSoapRequest('OCAPI10_Session', $opt['page']['absolute_url'] . 'xml/ocapi10');

	$nuserver->register('Login', array('user' => 'xsd:string', 
	                                   'pwmd5' => 'xsd:string'), 
	                             array('return' => 'xsd:string')); 
	$nuserver->register('IsValidSession', array('sessionid' => 'xsd:string'),
	                                      array('return' => 'xsd:boolean')); 
	$nuserver->register('Logout', array('sessionid' => 'xsd:string')); 

	finishSoapRequest();

/**
 * Method Login
 *
 * @param string $user username or email
 * @param string $pwMd5 MD5 of the password
 * @return string Id of the session
 */
function Login($user, $pwMd5)
{
	global $login;
	if ($err = initSoapFunction()) return $err;

	$nRet = $login->try_login_md5($user, $pwMd5, false);
	switch ($nRet)
	{
		case LOGIN_OK:
			return $login->sessionid;
			break;

		case LOGIN_EMPTY_USERPASSWORD:
			return new soap_fault(WS_ERR_LOGIN_EMPTY_USERPASSWORD_ID, '' , WS_ERR_LOGIN_EMPTY_USERPASSWORD_STR);
			break;

		case LOGIN_TOOMUCHLOGINS:
			return new soap_fault(WS_ERR_LOGIN_TOOMUCHLOGINS_ID, '' , WS_ERR_LOGIN_TOOMUCHLOGINS_STR);
			break;

		case LOGIN_USERNOTACTIVE:
			return new soap_fault(WS_ERR_LOGIN_USERNOTACTIVE_ID, '' , WS_ERR_LOGIN_USERNOTACTIVE_STR);
			break;

		case LOGIN_BADUSERPW:
			return new soap_fault(WS_ERR_LOGIN_BADUSERPW_ID, '' , WS_ERR_LOGIN_BADUSERPW_STR);
			break;

		default:
			return new soap_fault(WS_ERR_UNKOWN_ID, '' , WS_ERR_UNKOWN_STR);
	}
}

/**
 * Method IsValidSession
 *
 * @param string $sessionid Id of the session to check
 * @return boolean true for a valid session
 */
function IsValidSession($sessionid)
{
	global $login;
	if ($err = initSoapFunction()) return $err;

	return $login->restoreSession($sessionid);
}

/**
 * Logout method
 *
 * @param string $sessionid Id of the session to release
 * @return void No return value
 */
function Logout($sessionid)
{
	global $login;
	if ($err = initSoapFunction()) return $err;

	if ($login->restoreSession($sessionid))
		$login->logout();
}
?>