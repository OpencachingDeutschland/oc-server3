<?php

class Cache_Where
{
	public static function active()
	{
		return ' caches.status = ' . Cache_Status::Active . ' AND (caches.date_activate IS NULL OR caches.date_activate <= NOW()) ';
	}

	public static function publishNow()
	{
		return ' caches.status = ' . Cache_Status::NotYetPubliced . ' AND caches.date_activate <= NOW() ';
	}
}

?>
