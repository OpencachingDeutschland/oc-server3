<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  Business layer constant definitions
 ***************************************************************************/

define('EUROPEAN_LETTERS', 'A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ');
// ASCII + ISO-8859-1 0xC0..0xFF
define('REGEX_USERNAME', '^[' . EUROPEAN_LETTERS . '0-9\.\-_@=)(\/\\\&*+~#][' . EUROPEAN_LETTERS . '0-9\.\-_ @=)(\/\\\&*+~#]{1,58}[' . EUROPEAN_LETTERS . '0-9\.\-_@=)(\/\\\&*+~#]$');
define('REGEX_PASSWORD', '^[' . EUROPEAN_LETTERS . '0-9\.\-_ @=)(\/\\\&*+~#]{3,60}$');
define('REGEX_LAST_NAME', '^[' . EUROPEAN_LETTERS . '][' . EUROPEAN_LETTERS . '0-9\.\- ]{0,58}[' . EUROPEAN_LETTERS . '0-9\.]$');
define('REGEX_FIRST_NAME', REGEX_LAST_NAME);
define('REGEX_STATPIC_TEXT', '^[' . EUROPEAN_LETTERS . '0-9\.\-_ @=)(\/\\\&*\$+~#!§%;,-?:\[\]{}¹²³\'\"`\|µ°\%]{0,30}$');

define('ADMIN_TRANSLATE', 1); // edit translation
define('ADMIN_MAINTAINANCE', 2); // check table etc.
define('ADMIN_USER', 4); // drop users, caches etc.
define('ADMIN_NEWS', 8); // obsolete / reserved
define('ADMIN_RESTORE', 16); // restore vandalized listings
define('ADMIN_ROOT', 128 | 127); // root + all previous rights
define('ADMIN_LISTING', 1024); // can edit any cache listings

define('ATTRIB_SELECTED', 1);
define('ATTRIB_UNSELECTED', 2);
define('ATTRIB_UNDEF', 3);

define('ATTRIB_ID_SAFARI', 61);

define('OBJECT_CACHELOG', 1);
define('OBJECT_CACHE', 2);
define('OBJECT_CACHEDESC', 3);
define('OBJECT_USER', 4);
define('OBJECT_TRAVELER', 5);
define('OBJECT_PICTURE', 6);
define('OBJECT_REMOVEDOBJECT', 7);
define('OBJECT_WAYPOINT', 8);
define('OBJECT_CACHELIST', 9);

// coordinate types
define('COORDINATE_WAYPOINT', 1);
define('COORDINATE_USERNOTE', 2);

define('MAX_LOGENTRIES_ON_CACHEPAGE', 5);

// threshold for caches to be marked as "new"
define('NEWCACHES_DAYS', 31);

// constants for user options (must match values in DB!)
define('USR_OPT_GMZOOM', 1);
define('USR_OPT_SHOWSTATS', 5);
define('USR_OPT_MAP_MENU', 6);
define('USR_OPT_MAP_OVERVIEW', 7); // obsolete, no longer supported since Google Maps 3.22
define('USR_OPT_MAP_MAXCACHES', 8);
define('USR_OPT_MAP_ICONSET', 9);
define('USR_OPT_MAP_PREVIEW', 10);
define('USR_OPT_PICSTAT', 11);
define('USR_OPT_TRANSLANG', 12);
define('USR_OPT_OCONLY81', 13);
define('USR_OPT_LOG_AUTOLOAD', 14);
// ID 15 was temporarily used and is reserved, see commit dda7ef0. Continue with 16.

// user.data_license values
define('OLD_DATA_LICSENSE', 0); // before deadline
define('NEW_DATA_LICENSE_ACTIVELY_DECLINED', 1); // declined license
define('NEW_DATA_LICENSE_ACTIVELY_ACCEPTED', 2); // accepted new license on registration
define('NEW_DATA_LICENSE_PASSIVELY_ACCEPTED', 3); // did not decline license until deadline
define('NEW_DATA_LICENSE_PASSIVELY_DECLINED', 4); // could not accept/decline because disabled

//picture upload/resize parameters
define('PICTURE_QUALITY', 85);
define('PICTURE_RESOLUTION', 72);
define('PICTURE_MAX_LONG_SIDE', 1024);

// cache report status; see cache_report_status.sql
define('CACHE_REPORT_NEW', 1);
define('CACHE_REPORT_INPROGRESS', 2);
define('CACHE_REPORT_DONE', 3);
