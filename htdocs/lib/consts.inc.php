<?php
/****************************************************************************
 		 ./lib/consts.inc.php
 		-------------------
		begin                : Thu December 29 2005

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

		Unicode Reminder メモ

	named consts

 ****************************************************************************/

	define('GUI_HTML', 0);   // also defined in lib2/const.inc.php
	define('GUI_TEXT', 1);

	define('HTTPS_DISABLED', 0);   // also defined in lib2/const.inc.php
	define('HTTPS_ENABLED', 1);
	define('HTTPS_ENFORCED', 2);

	define('EMAIL_LINEWRAP', 72);

	// for cachelists
	define('cachelist_type_ignore', 1);
	define('cachelist_type_watch', 2);
	define('cachelist_type_series', 3);
	define('cachelist_type_user', 4);
	define('cachelist_type_other', 5);

	define('cachelist_nopermission', 0);
	define('cachelist_owner', 1);
	define('cachelist_mod', 2);

	// for notifications
	define('notify_new_cache', 1);
	define('notify_new_oconly', 2);

	// for ratings
	define('rating_percentage', 10); // percentage of found caches to be rated
?>