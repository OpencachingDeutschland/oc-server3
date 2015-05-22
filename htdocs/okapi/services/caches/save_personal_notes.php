<?php

namespace okapi\services\caches\save_personal_notes;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiAccessToken;
use okapi\Settings;


class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 3
        );
    }

    public static function call(OkapiRequest $request)
    {
        # Get current notes, and verify cache_code

        $cache_code = $request->get_parameter('cache_code');
        if ($cache_code == null)
            throw new ParamMissing('cache_code');
        $geocache = OkapiServiceRunner::call(
            'services/caches/geocache',
            new OkapiInternalRequest($request->consumer, $request->token, array(
                'cache_code' => $cache_code,
                'fields' => 'my_notes|internal_id'
            ))
        );
        $current_value = $geocache['my_notes'];
        if ($current_value == null) {
            $current_value = "";
        }
        $cache_id = $geocache['internal_id'];

        # old_value

        $old_value = $request->get_parameter('old_value');
        if ($old_value === null)
            $old_value = '';

        # new_value (force "no HTML" policy).

        $new_value = $request->get_parameter('new_value');
        if ($new_value === null)
            throw new ParamMissing('new_value');

        # Force "no HTML" policy.

        $new_value = strip_tags($new_value);

        # Placeholders for returned values.

        $ret_saved_value = null;
        $ret_replaced = false;

        if (
            trim($current_value) == "" ||
            self::str_equals($old_value, $current_value)
        ) {
            /* REPLACE mode */

            $ret_replaced = true;
            if (trim($new_value) == "") {
                /* empty new value means delete */
                self::remove_notes($cache_id, $request->token->user_id);
                $ret_saved_value = null;
            } else {
                self::update_notes($cache_id, $request->token->user_id, $new_value);
                $ret_saved_value = $new_value;
            }

        } else {

            /* APPEND mode */

            $ret_saved_value = trim($current_value)."\n\n".trim($new_value);
            self::update_notes($cache_id, $request->token->user_id, $ret_saved_value);
        }

        $result = array(
            'saved_value' => $ret_saved_value,
            'replaced' => $ret_replaced
        );
        return Okapi::formatted_response($request, $result);
    }

    private static function str_equals($str1, $str2)
    {
        if ($str1 == null)
            $str1 = '';
        if ($str2 == null)
            $str2 = '';
        $str1 = mb_ereg_replace("[ \t\n\r\x0B]+", '', $str1);
        $str2 = mb_ereg_replace("[ \t\n\r\x0B]+", '', $str2);

        return $str1 == $str2;
    }

    private static function update_notes($cache_id, $user_id, $new_notes)
    {
        if (Settings::get('OC_BRANCH') == 'oc.de')
        {
            /* See:
             *
             * - https://github.com/OpencachingDeutschland/oc-server3/tree/master/htdocs/libse/CacheNote
             * - http://www.opencaching.de/okapi/devel/dbstruct
             */

            $rs = Db::query("
                select max(id) as id
                from coordinates
                where
                    type = 2  -- personal note
                    and cache_id = '".mysql_real_escape_string($cache_id)."'
                    and user_id = '".mysql_real_escape_string($user_id)."'
            ");
            $id = null;
            if($row = mysql_fetch_assoc($rs)) {
                $id = $row['id'];
            }
            if ($id == null) {
                Db::query("
                    insert into coordinates (
                        type, latitude, longitude, cache_id, user_id,
                        description
                    ) values (
                        2, 0, 0,
                        '".mysql_real_escape_string($cache_id)."',
                        '".mysql_real_escape_string($user_id)."',
                        '".mysql_real_escape_string($new_notes)."'
                    )
                ");
            } else {
                Db::query("
                    update coordinates
                    set description = '".mysql_real_escape_string($new_notes)."'
                    where
                        id = '".mysql_real_escape_string($id)."'
                        and type = 2
                ");
            }
        }
        else  # oc.pl branch
        {
            $rs = Db::query("
                select max(note_id) as id
                from cache_notes
                where
                    cache_id = '".mysql_real_escape_string($cache_id)."'
                    and user_id = '".mysql_real_escape_string($user_id)."'
            ");
            $id = null;
            if($row = mysql_fetch_assoc($rs)) {
                $id = $row['id'];
            }
            if ($id == null) {
                Db::query("
                    insert into cache_notes (
                        cache_id, user_id, date, desc_html, `desc`
                    ) values (
                        '".mysql_real_escape_string($cache_id)."',
                        '".mysql_real_escape_string($user_id)."',
                        NOW(), 0,
                        '".mysql_real_escape_string($new_notes)."'
                    )
                ");
            } else {
                Db::query("
                    update cache_notes
                    set
                        `desc` = '".mysql_real_escape_string($new_notes)."',
                        desc_html = 0,
                        date = NOW()
                    where note_id = '".mysql_real_escape_string($id)."'
                ");
            }
        }
    }

    private static function remove_notes($cache_id, $user_id)
    {
        if (Settings::get('OC_BRANCH') == 'oc.de') {
            # we can delete row if and only if there are no coords in it
            Db::execute("
                delete from coordinates
                where
                    type = 2  -- personal note
                    and cache_id = '".mysql_real_escape_string($cache_id)."'
                    and user_id = '".mysql_real_escape_string($user_id)."'
                    and longitude = 0
                    and latitude = 0
            ");
            if (Db::get_affected_row_count() <= 0){
                # no rows deleted - record either doesn't exist, or has coords
                # remove only description
                Db::execute("
                    update coordinates
                    set description = null
                    where
                        type = 2
                        and cache_id = '".mysql_real_escape_string($cache_id)."'
                        and user_id = '".mysql_real_escape_string($user_id)."'
                ");
            }
        } else {  # oc.pl branch
            Db::execute("
                delete from cache_notes
                where
                    cache_id = '".mysql_real_escape_string($cache_id)."'
                    and user_id = '".mysql_real_escape_string($user_id)."'
            ");
        }
    }
}
