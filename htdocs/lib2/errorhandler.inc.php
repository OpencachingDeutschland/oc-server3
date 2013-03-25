<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  This is included from both lib1 and lib2.
 ***************************************************************************/

$error_handled = false;


function register_errorhandlers()
{
	set_error_handler('errorhandler', E_ERROR);
	register_shutdown_function('shutdownhandler');
}

function errorhandler($errno, $errstr, $errfile, $errline)
{
	// will catch a few runtime errors

	global $error_handled;

	if (!$error_handled)
	{
		$error_handled = true;
		$errtitle = "PHP-Fehler";

		$error = "($errno) $errstr at line $errline in $errfile";
		send_errormail($error);

		if (display_error())
			$errmsg = $error;
		else
			$errmsg = "";

		require(dirname(__FILE__) . "/../html/error.php");
	  exit;
  }
}

function shutdownhandler()
{
	// see http://stackoverflow.com/questions/1900208/php-custom-error-handler-handling-parse-fatal-errors
	//
	// will catch anything but parse errors

	global $error_handled;

	if (!$error_handled &&
	    function_exists("error_get_last") && /* PHP >= 5.2.0 */ ($error = error_get_last()) &&
	    in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR)))
	{
		$error_handled = true;

		$error = "(" . $error['type'] . ") " . $error['message'] . 
			          " at line " . $error['line'] . " of " . $error['file'];
		send_errormail($error);

		$errtitle = "PHP-Fehler";
		if (display_error())
			$errmsg = $error;
		else
			$errmsg = "";

		require(dirname(__FILE__) . "/../html/error.php");
	}
}

function display_error()
{
	global $opt, $debug_page;
	return (isset($opt['db']['error']['display']) && $opt['db']['error']['display']) ||
	       (isset($debug_page) && $debug_page);
}

function send_errormail($errmsg)
{
	global $opt, $sql_errormail, $absolute_server_URI;

	if (isset($opt['db']['error']['mail']) && $opt['db']['error']['mail'] != '')
	{
		@mb_send_mail($opt['db']['error']['mail'], $opt['mail']['subject'] . " PHP error", $errmsg);
	}
	else if (isset($sql_errormail) && $sql_errormail != '')
	{
		$url = parse_url($absolute_server_URI);
		@mb_send_mail($sql_errormail, "[" . $url['host'] . "] PHP error", $errmsg);
	}
}

?>