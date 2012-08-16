<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Constant definitions
 ***************************************************************************/

	define('GUI_HTML', 0);
	define('GUI_TEXT', 1);
	define('GUI_NUSOAP', 2);

	define('DEBUG_NO', 0);
	define('DEBUG_DEVELOPER', 1);
	define('DEBUG_TEMPLATES', 2);
	define('DEBUG_OUTOFSERVICE', 4 | DEBUG_TEMPLATES);
	define('DEBUG_TESTING', 8 | DEBUG_TEMPLATES);
	define('DEBUG_SQLDEBUGGER', 16);
	define('DEBUG_TRANSLATE', 32); // DEBUG_TEMPLATES added in common.inc.php
	define('DEBUG_FORCE_TRANSLATE', 64 | DEBUG_TRANSLATE);
	define('DEBUG_CLI', 128);

	define('PHP_DEBUG_OFF', 0);
	define('PHP_DEBUG_ON', 1);
	define('PHP_DEBUG_SKIP', -1);

	define('SAVE_COOKIE', 0);
	define('SAVE_SESSION', 1);

	define('DB_MODE_FRAMEWORK', 0);
	define('DB_MODE_BUSINESSLAYER', 1);
	define('DB_MODE_USER', 2);
	define('DB_DATE_FORMAT', '%Y-%m-%d %H:%M:%S');

	// constants for user options (must match values in DB!)
	define('USR_OPT_GMZOOM', 1);
	define('USR_OPT_SHOWSTATS', 5);
	define('USR_OPT_TRANSLANG', 6);
?>
