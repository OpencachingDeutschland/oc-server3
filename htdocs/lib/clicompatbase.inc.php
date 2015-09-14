<?php
/****************************************************************************
													 ./lib/clicompatbase.inc.php
															--------------------
		begin                : Fri September 16 2005

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

		Unicode Reminder メモ

	contains functions that are compatible with the php-CLI-scripts under util.
	Can be included without including common.inc.php, but will be included from
	common.inc.php.

	Global variables that need to be set up when including without common.inc.php:

	$dblink

 ****************************************************************************/

	global $interface_output, $dblink_slave;
	if (!isset($interface_output)) $interface_output = 'plain';

	if (isset($opt['rootpath']))
		$rootpath = $opt['rootpath'];
	else if (isset($rootpath))
		$opt['rootpath'] = $rootpath;
	else
	{
		$rootpath = './';
		$opt['rootpath'] = $rootpath;
	}

	// yepp, we will use UTF-8
	mb_internal_encoding('UTF-8');
	mb_regex_encoding('UTF-8');
	mb_language('uni');

 	// language unspecific constants
	define('regex_username', '^[a-zA-Z0-9][a-zA-Z0-9\.\-_ @äüöÄÜÖ=)(\/\\\&*+~#]{2,59}$');
	define('regex_password', '^[a-zA-Z0-9\.\-_ @äüöÄÜÖ=)(\/\\\&*+~#]{3,60}$');
	define('regex_last_name', '^[a-zA-Z][a-zA-Z0-9\.\- äüöÄÜÖ]{1,59}$');
	define('regex_first_name', '^[a-zA-Z][a-zA-Z0-9\.\- äüöÄÜÖ]{1,59}$');
	define('regex_statpic_text', '^[a-zA-Z0-9\.\-_ @äüöÄÜÖß=)(\/\\\&*\$+~#!§%;,-?:\[\]{}¹²³\'\"`\|µ°]{0,29}$');

	//load default webserver-settings and common includes
	require_once($opt['rootpath'] . 'lib/consts.inc.php');
	require_once($opt['rootpath'] . 'lib/settings.inc.php');
	require_once($opt['rootpath'] . 'lib2/errorhandler.inc.php');

	// check for banned UAs
	$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
	foreach ($opt['page']['banned_user_agents'] as $ua)
		if (strpos($useragent, $ua) !== false)
			die();

	// basic PHP settings
	date_default_timezone_set($timezone);
	if ($debug_page)
		register_errorhandlers();  // not for production use yet, must be tested

	$dblink_slave = false;
	$db_error = 0;

	// sql debugger?
	if (!isset($sql_allow_debug)) $sql_allow_debug = 0;

	// prepare EMail-From
	$emailheaders = 'From: "' . $emailaddr . '" <' . $emailaddr . '>';

	function logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
	{
		sql("INSERT INTO logentries (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`) VALUES (
																 '&1', '&2', '&3', '&4', '&5', '&6', '&7')",
																 $module, $eventid, $userid, $objectid1, $objectid2, $logtext, serialize($details));
	}

	// read a file and return it as a string
	// WARNING: no huge files!
	function read_file($file='')
	{
		$fh = fopen($file, 'r');
		if ($fh)
		{
			$content = fread($fh, filesize($file));
		}

		fclose($fh);

		return $content;
	}

	// explode with more than one separator
	function explode_multi($str, $sep)
	{
		$ret = array();
		$nCurPos = 0;

		while ($nCurPos < mb_strlen($str))
		{
			$nNextSep = mb_strlen($str);
			for ($nSepPos = 0; $nSepPos < mb_strlen($sep); $nSepPos++)
			{
				$nThisPos = mb_strpos($str, mb_substr($sep, $nSepPos, 1), $nCurPos);
				if ($nThisPos !== false)
					if ($nNextSep > $nThisPos)
						$nNextSep = $nThisPos;
			}

			$ret[] = mb_substr($str, $nCurPos, $nNextSep - $nCurPos);

			$nCurPos = $nNextSep + 1;
		}

		return $ret;
	}

	function mb_strpos_multi($haystack, $needles)
	{
		$arg = func_get_args();
		$start = false;

		foreach ($needles AS $needle)
		{
			$thisstart = mb_strpos($haystack, $needle, $arg[2]);
			if ($start == false)
				$start = $thisstart;
			else if ($thisstart == false)
			{
			}
			else if ($start > $thisstart)
				$start = $thisstart;
		}

		return $start;
	}
	
	function escape_javascript($text)
	{
		return str_replace('\'', '\\\'', str_replace('"', '&quot;', $text));
	}

	// called if mysql_query faild, sends email to sysadmin
	function sql_failed($sql)
	{
		sql_error();
	}

	function sqlValue($sql, $default)
	{
		$rs = sql($sql);
		if ($r = sql_fetch_row($rs))
		{
			if ($r[0] == null)
				return $default;
			else
				return $r[0];
		}
		else
			return $default;
	}

	function sql_value_slave($sql, $default)
	{
		$rs = sql_slave($sql);
		if ($r = sql_fetch_row($rs))
		{
			if ($r[0] == null)
				return $default;
			else
				return $r[0];
		}
		else
			return $default;
	}

	function getSysConfig($name, $default)
	{
		return sqlValue('SELECT `value` FROM `sysconfig` WHERE `name`=\'' . sql_escape($name) . '\'', $default);
	}

	function setSysConfig($name, $value)
	{
		if (sqlValue('SELECT COUNT(*) FROM sysconfig WHERE name=\'' . sql_escape($name) . '\'', 0) == 1)
			sql("UPDATE `sysconfig` SET `value`='&1' WHERE `name`='&2' LIMIT 1", $value, $name);
		else
			sql("INSERT INTO `sysconfig` (`name`, `value`) VALUES ('&1', '&2')", $name, $value);
	}

	/*
		sql("SELECT id FROM &tmpdb.table WHERE a=&1 AND &tmpdb.b='&2'", 12345, 'abc');

		returns: recordset or false
	*/
	function sql($sql)
	{
		global $dblink;

		// prepare args
		$args = func_get_args();
		unset($args[0]);

		if (isset($args[1]) && is_array($args[1]))
		{
			$tmp_args = $args[1];
			unset($args);

			// correct indizes
			$args = array_merge(array(0), $tmp_args);
			unset($tmp_args);
			unset($args[0]);
		}

		return sql_internal($dblink, $sql, false, $args);
	}

	function sql_slave($sql)
	{
		global $dblink_slave;

		// prepare args
		$args = func_get_args();
		unset($args[0]);

		if (isset($args[1]) && is_array($args[1]))
		{
			$tmp_args = $args[1];
			unset($args);

			// correct indizes
			$args = array_merge(array(0), $tmp_args);
			unset($tmp_args);
			unset($args[0]);
		}

		if ($dblink_slave === false)
			db_connect_anyslave();

		return sql_internal($dblink_slave, $sql, true, $args);
	}

	function sql_internal($_dblink, $sql, $bSlave)
	{
		global $opt;
		global $sql_debug, $sql_warntime;
		global $sql_replacements;
		global $sqlcommands;
		global $dblink_slave;

		$args = func_get_args();
		unset($args[0]);
		unset($args[1]);
		unset($args[2]);

		/* as an option, you can give as second parameter an array
		 * with all values for the placeholder. The array has to be
		 * with numeric indizes.
		 */
		if (isset($args[3]) && is_array($args[3]))
		{
			$tmp_args = $args[3];
			unset($args);
			
			// correct indizes
			$args = array_merge(array(0), $tmp_args);
			unset($tmp_args);
			unset($args[0]);
		}

		$sqlpos = 0;
		$filtered_sql = '';

		// $sql von vorne bis hinten durchlaufen und alle &x ersetzen
		$nextarg = mb_strpos($sql, '&');
		while ($nextarg !== false)
		{
			// muss dieses & ersetzt werden, oder ist es escaped?
			$escapesCount = 0;
			while ((($nextarg - $escapesCount - 1) > 0) && (mb_substr($sql, $nextarg - $escapesCount - 1, 1) == '\\')) $escapesCount++;
			if (($escapesCount % 2) == 1)
				$nextarg++;
			else
			{
				$nextchar = mb_substr($sql, $nextarg + 1, 1);
				if (is_numeric($nextchar))
				{
					$arglength = 0;
					$arg = '';

					// nächstes Zeichen das keine Zahl ist herausfinden
					while (mb_ereg_match('^[0-9]{1}', $nextchar) == 1)
					{
						$arg .= $nextchar;

						$arglength++;
						$nextchar = mb_substr($sql, $nextarg + $arglength + 1, 1);
					}

					// ok ... ersetzen
					$filtered_sql .= mb_substr($sql, $sqlpos, $nextarg - $sqlpos);
					$sqlpos = $nextarg + $arglength;

					if (isset($args[$arg]))
					{
						if (is_numeric($args[$arg]))
							$filtered_sql .= $args[$arg];
						else
						{
							if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '\'') && (mb_substr($sql, $sqlpos + 1, 1) == '\''))
								$filtered_sql .= sql_escape($args[$arg]);
							else if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '`') && (mb_substr($sql, $sqlpos + 1, 1) == '`'))
								$filtered_sql .= sql_escape($args[$arg]);
							else
								sql_error();
						}
					}
					else
					{
						// NULL
						if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '\'') && (mb_substr($sql, $sqlpos + 1, 1) == '\''))
						{
							// Anführungszeichen weg machen und NULL einsetzen
							$filtered_sql = mb_substr($filtered_sql, 0, mb_strlen($filtered_sql) - 1);
							$filtered_sql .= 'NULL';
							$sqlpos++;
						}
						else
							$filtered_sql .= 'NULL';
					}

					$sqlpos++;
				}
				else
				{
					$arglength = 0;
					$arg = '';

					// nächstes Zeichen das kein Buchstabe/Zahl ist herausfinden
					while (mb_ereg_match('^[a-zA-Z0-9]{1}', $nextchar) == 1)
					{
						$arg .= $nextchar;

						$arglength++;
						$nextchar = mb_substr($sql, $nextarg + $arglength + 1, 1);
					}

					// ok ... ersetzen
					$filtered_sql .= mb_substr($sql, $sqlpos, $nextarg - $sqlpos);

					if (isset($sql_replacements[$arg]))
					{
						$filtered_sql .= $sql_replacements[$arg];
					}
					else
						sql_error();

					$sqlpos = $nextarg + $arglength + 1;
				}
			}

			$nextarg = mb_strpos($sql, '&', $nextarg + 1);
		}

		// rest anhängen
		$filtered_sql .= mb_substr($sql, $sqlpos);

		// \& durch & ersetzen
		$nextarg = mb_strpos($filtered_sql, '\&');
		while ($nextarg !== false)
		{
			$escapesCount = 0;
			while ((($nextarg - $escapesCount - 1) > 0) && (mb_substr($filtered_sql, $nextarg - $escapesCount - 1, 1) == '\\')) $escapesCount++;
			if (($escapesCount % 2) == 0)
			{
				// \& ersetzen durch &
				$filtered_sql = mb_substr($filtered_sql, 0, $nextarg) . '&' . mb_substr($filtered_sql, $nextarg + 2);
				$nextarg--;
			}

			$nextarg = mb_strpos($filtered_sql, '\&', $nextarg + 2);
		}

		//
		// ok ... hier ist filtered_sql fertig
		//

		/* todo:
			- errorlogging
			- LIMIT
			- DROP/DELETE ggf. blocken
		*/

		if (isset($sql_debug) && ($sql_debug == true))
		{
			require_once($opt['rootpath'] . 'lib/sqldebugger.inc.php');
			$result = sqldbg_execute($filtered_sql, $bSlave);
			if ($result === false) sql_error();
		}
		else
		{
			// Zeitmessung für die Ausführung
			require_once($opt['rootpath'] . 'lib/bench.inc.php');
			$cSqlExecution = new Cbench;
			$cSqlExecution->start();

			$result = mysql_query($filtered_sql, $_dblink);
			if ($result === false) sql_error();

			$cSqlExecution->stop();

			if ($sql_warntime>0 && $cSqlExecution->diff() > $sql_warntime)
			{
				$ua = isset($_SERVER['HTTP_USER_AGENT']) ? "\r\n" . $_SERVER['HTTP_USER_AGENT'] : "";
				sql_warn("execution took " . $cSqlExecution->diff() . " seconds" . $ua);
			}
		}

		return $result;
	}

	function sql_escape($value)
	{
		global $dblink;
		$value = mysql_real_escape_string($value, $dblink);
		$value = mb_ereg_replace('&', '\&', $value);
		return $value;
	}

	function sql_escape_backtick($value)
	{
		$value = sql_escape($value);
		$value = str_replace('`', '``', $value);
		return $value;
	}

	function sql_error()
	{
		global $debug_page;
		global $sql_errormail;
		global $emailheaders;
		global $absolute_server_URI;
		global $interface_output;
		global $dberrormsg;
		global $db_error;

		$db_error += 1;
		$msql_error = mysql_errno() . ": " . mysql_error();
		if ($db_error > 1)
			$msql_error .= "\n(** error recursion **)";

		if ($sql_errormail != '')
		{
			// sendout email
			$email_content = $msql_error;
			$email_content .= "\n--------------------\n";
			$email_content .= print_r(debug_backtrace(), true);
			if (admin_errormail($sql_errormail, 'sql_error', $email_content, $emailheaders))
				mb_send_mail($sql_errormail, 'sql_error: ' . $absolute_server_URI, $email_content, $emailheaders);
		}

		if ($interface_output == 'html')
		{
			// display errorpage
			$errmsg = $dberrormsg . ($debug_page ? "<br />" . $msql_error : "");
			if ($db_error <= 1)
			{
				tpl_errorMsg('sql_error', $errmsg);
			}
			else
			{
				// datbase error recursion, because another error occured while trying to
				// build the error template (e.g. because connection was lost, or an error mail
				// could not load translations from database)

				$errtitle = "Datenbankfehler";
				require("html/error.php");
			}
			exit;
		}
		else if ($interface_output == 'plain')
		{
			echo "\n";
			echo 'sql_error' . "\n";
			if ($debug_page) echo $msql_error . "\n";
			echo '---------' . "\n";
			echo print_r(debug_backtrace(), true) . "\n";
			exit;
		}

		die('sql_error');
	}

	function sql_warn($warnmessage)
	{
		global $sql_errormail;
		global $emailheaders;
		global $absolute_server_URI;

		$email_content = $warnmessage;
		$email_content .= "\n--------------------\n";
		$email_content .= print_r(debug_backtrace(), true);

		if (admin_errormail($sql_errormail, 'sql_warn', $email_content, $emailheaders))
			@mb_send_mail($sql_errormail, 'sql_warn: ' . $absolute_server_URI, $email_content, $emailheaders);
	}

	/*
		Ersatz für die in Mysql eingebauten Funktionen
	*/
	function sql_fetch_array($rs)
	{
		return mysql_fetch_array($rs);
	}

	function sql_fetch_assoc($rs)
	{
		return mysql_fetch_assoc($rs);
	}

	function sql_fetch_row($rs)
	{
		return mysql_fetch_row($rs);
	}

	function sql_free_result($rs)
	{
		return mysql_free_result($rs);
	}
	
	function mb_trim($str)
	{
		$bLoop = true;
		while ($bLoop == true)
		{
			$sPos = mb_substr($str, 0, 1);
			
			if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
				$str = mb_substr($str, 1, mb_strlen($str) - 1);
			else
				$bLoop = false;
		}

		$bLoop = true;
		while ($bLoop == true)
		{
			$sPos = mb_substr($str, -1, 1);
			
			if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
				$str = mb_substr($str, 0, mb_strlen($str) - 1);
			else
				$bLoop = false;
		}

		return $str;
	}

	//disconnect the databse
	function db_disconnect()
	{
		global $dbpconnect, $dblink, $dblink_slave, $dbslaveid;

		//is connected and no persistent connect used?
		if (($dbpconnect == false) && ($dblink !== false))
		{
			@mysql_close($dblink);
			$dblink = false;
		}
		if (($dbpconnect == false) && ($dblink_slave !== false))
		{
			@mysql_close($dblink_slave);
			$dblink_slave = false;
			$dbslaveid = -1;
		}
	}

	//database handling
	function db_connect()
	{
		global $dblink, $dbpconnect, $dbusername, $dbname, $dbserver, $dbpasswd, $dbpconnect;

		//connect to the database by the given method - no php error reporting!
		if ($dbpconnect == true)
		{
			$dblink = @mysql_pconnect($dbserver, $dbusername, $dbpasswd);
		}
		else
		{
			$dblink = @mysql_connect($dbserver, $dbusername, $dbpasswd);
		}

		if ($dblink != false)
		{
			mysql_query("SET NAMES 'utf8'", $dblink);

			//database connection established ... set the used database
			if (@mysql_select_db($dbname, $dblink) == false)
			{
				//error while setting the database ... disconnect
				db_disconnect();
				$dblink = false;
			}
		}
	}
	
	function db_slave_exclude()
	{
		global $usr;
		if ($usr === false)
			return;

		sql("INSERT INTO `sys_repl_exclude` (`user_id`, `datExclude`) VALUES ('&1', NOW())
		            ON DUPLICATE KEY UPDATE `datExclude`=NOW()", $usr['userid']);
	}

	function db_connect_anyslave()
	{
		global $dblink, $dblink_slave, $opt, $usr, $dbslaveid;

		if ($dblink_slave !== false)
			return;

		$nMaxTimeDiff = $opt['db']['slave']['max_behind'];
		if ($usr !== false)
		{
			$nMaxTimeDiff = sqlValue("SELECT TIMESTAMP(NOW())-TIMESTAMP(`datExclude`) FROM `sys_repl_exclude` WHERE `user_id`='" . ($usr['userid']+0) . "'", $opt['db']['slave']['max_behind']);
			if ($nMaxTimeDiff > $opt['db']['slave']['max_behind'])
				$nMaxTimeDiff = $opt['db']['slave']['max_behind'];
		}

		$id = sqlValue("SELECT `id`, `weight`*RAND() AS `w` FROM `sys_repl_slaves` WHERE `active`=1 AND `online`=1 AND (TIMESTAMP(NOW())-TIMESTAMP(`last_check`)+`time_diff`<'" . ($nMaxTimeDiff+0) . "') ORDER BY `w` DESC LIMIT 1", -1);

		if ($id == -1)
		{
			$dblink_slave = $dblink;
			$dbslaveid = -1;
		}
		else
		{
			db_connect_slave($id);
		}
	}

	function db_connect_primary_slave()
	{
		global $opt, $dblink, $dblink_slave, $dbslaveid;

		if ($opt['db']['slave']['primary'] == -1)
		{
			$dblink_slave = $dblink;
			$dbslaveid = -1;
		}
		else
			db_connect_slave($opt['db']['slave']['primary']);
	}

	function db_connect_slave($id)
	{
		global $opt, $dblink_slave, $dbpconnect, $dbname, $dbslaveid;

		// the right slave is connected
		if ($dblink_slave !== false)
		{
			// TODO: disconnect if other slave is connected
			return;
		}

		$slave = $opt['db']['slaves'][$id];
		
		if ($dbpconnect == true)
			$dblink_slave = @mysql_pconnect($slave['server'], $slave['username'], $slave['password']);
		else
			$dblink_slave = @mysql_connect($slave['server'], $slave['username'], $slave['password']);

		if ($dblink_slave !== false)
		{
			$dbslaveid = $id;
			if (mysql_select_db($dbname, $dblink_slave) == false)
				sql_error();
			mysql_query("SET NAMES 'utf8'", $dblink_slave);
		}
		else
			sql_error();
	}

	function fetch_email_template($filename, $language)
	{
		global $opt, $rootpath;

		if (!$language) $language = $opt['template']['default']['locale'];
		$language = strtolower($language);
		if (!file_exists("$rootpath/lang/de/ocstyle/email/$language/$filename.email"))
			$language = 'en';
		return read_file("$rootpath/lang/de/ocstyle/email/$language/$filename.email");
	}

?>
