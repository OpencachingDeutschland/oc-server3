<?php
/***************************************************************************
											     ./lib/eventhandler.inc.php
															-------------------
		begin                : Mon June 28 2004
		copyright            : (C) 2004 The OpenCaching Group
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

	 handler for events like a new cache post or a new log post

	 add in the function all neccessary actions to refresh static files

 ****************************************************************************/
function delete_statpic($userid)
{
	$userid = $userid + 0;

	// data changed - delete statpic of user, if exists - will be recreated on next request
	if (file_exists('./images/statpics/statpic'.$userid.'.jpg'))
	{
		unlink('./images/statpics/statpic'.$userid.'.jpg');
	}
}

function event_new_cache($userid)
{
	delete_statpic($userid);
}

function event_new_log($cacheid, $userid)
{
	delete_statpic($userid);
}

function event_change_log_type($cacheid, $userid)
{
	delete_statpic($userid);
}

function event_remove_log($cacheid, $userid)
{
	delete_statpic($userid);
}

function event_edit_cache($cacheid, $userid)
{
	delete_statpic($userid);
}

function event_change_statpic($userid)
{
	delete_statpic($userid);
}

function event_notify_new_cache($cache_id)
{
}
?>