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

$locale_files = glob(dirname(__FILE__) . '/../config/locales/*.json', GLOB_NOSORT);

foreach ($locale_files as $locale_file) {
    $json_data = file_get_contents($locale_file);
    if (!is_string($json_data)) {
        file_put_contents('/tmp/ocde.log', 'Contents of file "' . $locale_file . '" could not be read!', FILE_APPEND);
        continue;
    }
    $data = json_decode($json_data, true);
    if (!is_array($data)) {
        file_put_contents('/tmp/ocde.log', 'Contents of file "' . $locale_file . '" is no JSON (Error ' . json_last_error() . ')!' . PHP_EOL . $json_data, FILE_APPEND);
        continue;
    }
    $pre_opt = array_merge_recursive($opt, $data);
    if (!is_array($pre_opt)) {
        file_put_contents('/tmp/ocde.log', 'Contents of file "' . $locale_file . '" could not be merged!', FILE_APPEND);
        continue;
    }
    $opt = $pre_opt;
}
