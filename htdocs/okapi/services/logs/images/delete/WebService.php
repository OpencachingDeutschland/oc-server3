<?php

namespace okapi\services\logs\images\delete;

use okapi\Db;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\services\logs\images\LogImagesCommon;
use okapi\Settings;


class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 3
        );
    }

    static function call(OkapiRequest $request)
    {
        list($image_uuid, $log_internal_id) = LogImagesCommon::validate_image_uuid($request);
        $image_uuid_escaped = Db::escape_string($image_uuid);

        Db::execute('start transaction');

        $image_row = Db::select_row("
            select id, node, url, local
            from pictures
            where uuid = '".$image_uuid_escaped."'
        ");
        Db::execute("
            delete from pictures where uuid = '".$image_uuid_escaped."'
        ");

        # Remember that OCPL picture sequence numbers are always 1, and
        # OCDE sequence numbers may have gaps. So we do not need to adjust
        # any numbers after deleting from table 'pictures'.

        if (Settings::get('OC_BRANCH') == 'oc.de')
        {
            # OCDE does all the housekeeping by triggers
        }
        else
        {
            Db::execute("
                INSERT INTO removed_objects (
                    localID, uuid, type, removed_date, node
                )
                VALUES (
                    ".$image_row['id']."
                    '".$image_uuid_escaped."',
                    6,
                    NOW(),
                    ".$image_row['node']
                    # OCPL code inserts the site's node ID here, which is wrong
                    # but currently is always the same as the image's node ID.
                    ."
                )
            ");

            # This will also update cache_logs.okapi_syncbase, so that replication
            # can output the updated log entry with one image less. For OCDE
            # that's done by DB trigges.

            Db::execute("
                update cache_logs
                set
                    picturescount = greatest(0, picturescount - 1),
                    last_modified = NOW()
                where id = '".Db::escape_string($log_internal_id)."'
            ");
        }

        Db::execute('commit');

        if ($image_row['local']) {
            $filename = basename($image_row['url']);
            unlink(Settings::get('IMAGES_DIR').'/'.$filename);
        }

        Okapi::update_user_activity($request);
        $result = array(
            'success' => true,
        );
        return Okapi::formatted_response($request, $result);
    }
}
