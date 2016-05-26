<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class Cronjobs
{
    // Cronjobs are disabled if the website is down (for maintenance).

    public static function enabled()
    {
        global $argv, $http_response_header, $opt;

        if (!in_array('--auto', $argv)) {
            // Cronjob is run manually (testing). We do it this way round
            // (not defaulting to 'auto' when no '--test' param is given),
            // because the effect of forgetting a --test parameter can be worse
            // than forgetting the --auto option in crontab.
            return true;
        } elseif (@file_get_contents($opt['page']['absolute_http_url'] . 'api/ping.php') !== false) {
            // website is up and running
            return true;
        } else {
            // === null: website is down, or DNS configuration error
            // !== null: website is access protected or page is redirected or whatever
            return $http_response_header !== null;
        }
    }
}
