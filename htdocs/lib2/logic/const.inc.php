<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Business layer constant definitions
 ***************************************************************************/

	define('ID_NEW', -1);

	define('RE_TYPE_INT', 1);
	define('RE_TYPE_STRING', 2);
	define('RE_TYPE_BOOLEAN', 3);
	define('RE_TYPE_DATE', 4);
	define('RE_TYPE_FLOAT', 5);
	define('RE_TYPE_DOUBLE', 6);

	define('RE_INSERT_NOTHING', 0);       // 
	define('RE_INSERT_OVERWRITE', 1);     // ignore given values and use function
	define('RE_INSERT_IGNORE', 2);        // dont use this column on insert
	define('RE_INSERT_AUTOINCREMENT', 4); // column is an auto increment column
	define('RE_INSERT_UUID', 8);          // UUID()
	define('RE_INSERT_NOW', 16);          // NOW()

	define('REGEX_USERNAME', '^[a-zA-Z0-9\.\-_@äüöÄÜÖ=)(\/\\\&*+~#][a-zA-Z0-9\.\-_ @äüöÄÜÖ=)(\/\\\&*+~#]{2,58}[a-zA-Z0-9\.\-_@äüöÄÜÖ=)(\/\\\&*+~#]$');
	define('REGEX_PASSWORD', '^[a-zA-Z0-9\.\-_ @äüöÄÜÖ=)(\/\\\&*+~#]{3,60}$');
	define('REGEX_LAST_NAME', '^[a-zA-Z][a-zA-Z0-9\.\- äüöÄÜÖ]{1,59}$');
	define('REGEX_FIRST_NAME', '^[a-zA-Z][a-zA-Z0-9\.\- äüöÄÜÖ]{1,59}$');
	define('REGEX_STATPIC_TEXT', '^[a-zA-Z0-9\.\-_ @äüöÄÜÖß=)(\/\\\&*\$+~#!§%;,-?:\[\]{}¹²³\'\"`\|µ°\%]{0,30}$');

	define('ADMIN_TRANSLATE', 1);     // edit translation
	define('ADMIN_MAINTAINANCE', 2);  // check table etc.
	define('ADMIN_USER', 4);          // drop users, caches etc.
	define('ADMIN_NEWS', 8);          // approve news entries
	define('ADMIN_ROOT', 128 | 127);  // root + all previous rights

	define('ATTRIB_SELECTED', 1);
	define('ATTRIB_UNSELECTED', 2);
	define('ATTRIB_UNDEF', 3);

	define('OBJECT_CACHELOG', 1);
	define('OBJECT_CACHE', 2);
	define('OBJECT_CACHEDESC', 3);
	define('OBJECT_USER', 4);
	define('OBJECT_TRAVELER', 5);
	define('OBJECT_PICTURE', 6);
	define('OBJECT_REMOVEDOBJECT', 7);
?>