<?php
/***************************************************************************
 		 ./lib/consts.inc.php
 		-------------------
		begin                : Thu December 29 2005
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

	named consts

 ****************************************************************************/

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

	// for ratings
	define('rating_percentage', 10); // percentage of found caches to be rated
?>