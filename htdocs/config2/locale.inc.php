<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Default settings for all locale options in settings.inc.php
 *  Do not modify this file - use settings.inc.php!
 *
 *  ATTENTION: This file is also used in old template system.
 *             (this means any call to framework functions may be incompatible)
 *
 *             Only set the following keys in $opt[]
 *
 *                 $opt['template']['locales']
 *                 $opt['geokrety']['locales']
 *                 $opt['locale']
 ***************************************************************************/

if (!isset($opt)) {
    $opt = array();
}

// backwards compatibility

$locale_files = glob(dirname(__FILE__) . '/../config/locales/*.json');

foreach ($locale_files as $locale_file) {
    $json_data = file_get_contents($locale_file);
    $data = json_decode($json_data, true);
    $opt = array_merge_recursive($opt, $data);
}
