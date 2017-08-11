<?php

namespace okapi\lib;

use okapi\Settings;
use okapi\Db;

/**
 * Use this class to access OC session variables. This is especially useful if
 * you want to determine which user is currently logged in to OC.
 */
class OCSession
{
    /** Return ID of currently logged in user or NULL if no user is logged in. */
    public static function get_user_id()
    {
        static $cached_result = false;
        if ($cached_result !== false) {
            return $cached_result;
        }

        $cookie_name = Settings::get('OC_COOKIE_NAME');
        if (!isset($_COOKIE[$cookie_name])) {
            return null;
        }

        $binary_content = base64_decode($_COOKIE[$cookie_name]);
        if ($binary_content === false) {
            return null;
        }

        $OC_data = json_decode($binary_content, true);

        if (!is_array($OC_data)) {
            return null;
        }

        if (!isset($OC_data['sessionid'])) {
            return null;
        }

        $OC_sessionid = $OC_data['sessionid'];
        if (!$OC_sessionid) {
            return null;
        }

        $cached_result = Db::select_value("
            select sys_sessions.user_id
            from
                sys_sessions,
                user
            where
                sys_sessions.uuid = '".Db::escape_string($OC_sessionid)."'
                and user.user_id = sys_sessions.user_id
                and user.is_active_flag = 1
        ");
        return $cached_result;
    }
}
