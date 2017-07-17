<?php

/**
 * Common code of add.php and edit.php
 */

namespace okapi\services\logs\images;

use Exception;
use okapi\Db;
use okapi\InvalidParam;
use okapi\ParamMissing;
use okapi\Settings;


class LogImagesCommon
{
    public function validate_image_uuid($request)
    {
        $image_uuid = $request->get_parameter('image_uuid');
        if (!$image_uuid)
            throw new ParamMissing('image_uuid');

        # When uploading images, OCPL stores the user_id of the uploader
        # in the 'pictures' table. This is redundant to cache_logs.user_id,
        # because only the log entry author may append images. We will stick
        # to log_entries.user_id here, which is the original value and works
        # for all OC branches, and ignore pictures.user_id.

        $rs = Db::query("
            select
                cache_logs.id log_internal_id,
                cache_logs.user_id,
                pictures.node
            from cache_logs
            join pictures on pictures.object_id = cache_logs.id
            where pictures.object_type = 1 and pictures.uuid = '".Db::escape_string($image_uuid)."'
        ");
        $row = Db::fetch_assoc($rs);
        Db::free_result($rs);
        if (!$row) {
            throw new InvalidParam(
                'image_uuid',
                "There is no log entry image with uuid '".$image_uuid."'."
            );
        }
        if ($row['node'] != Settings::get('OC_NODE_ID')) {
            throw new Exception(
                "This site's database contains the image '$image_uuid' which has been"
                . " imported from another OC node. OKAPI is not prepared for that."
            );
        }
        if ($row['user_id'] != $request->token->user_id) {
            throw new InvalidParam(
                'image_uuid',
                "The user of your access token is not the author of the associated log entry."
            );
        }

        return array($image_uuid, $row['log_internal_id']);
    }


    public function validate_position($request)
    {
        $position = $request->get_parameter('position');
        if ($position !== null && !preg_match("/^-?[0-9]+$/", $position)) {
            throw new InvalidParam('position', "'".$position."' is not an integer number.");
        }
        return $position;
    }


    /**
     * OCDE supports arbitrary ordering of log images. The pictures table
     * contains sequence numbers, which are always > 0 and need not to be
     * consecutive (may have gaps). There is a unique index which prevents
     * inserting duplicate seq numbers for the same log.
     *
     * OCPL sequence numbers currently are always = 1.
     *
     * The purpose of this function is to bring the supplied 'position'
     * parameter into bounds, and to calculate an appropriate sequence number
     * from it.
     *
     * This function is always called when adding images. When editing images,
     * it is called only for OCDE and if the position parameter was supplied.
     */

    static function prepare_position($log_internal_id, $position, $end_offset)
    {
        if (Settings::get('OC_BRANCH') == 'oc.de' && $position !== null)
        {
            # Prevent race conditions when creating sequence numbers if a
            # user tries to upload multiple images simultaneously. With a
            # few picture uploads per hour - most of them probably witout
            # a 'position' parameter - the lock is neglectable.

            Db::execute('lock tables pictures write');
        }

        $log_images_count =  Db::select_value("
            select count(*)
            from pictures
            where object_type = 1 and object_id = '".Db::escape_string($log_internal_id)."'
        ");

        if (Settings::get('OC_BRANCH') == 'oc.pl')
        {
            # Ignore the position parameter, always insert at end.
            # Remember that this function is NOT called when editing OCPL images.

            $position = $log_images_count;
            $seq = 1;
        }
        else
        {
            if ($position === null || $position >= $log_images_count) {
                $position = $log_images_count - 1 + $end_offset;
                $seq = Db::select_value("
                    select max(seq)
                    from pictures
                    where object_type = 1 and object_id = '".Db::escape_string($log_internal_id)."'
                ") + $end_offset;
            } else if ($position <= 0) {
                $position = 0;
                $seq = 1;
            } else {
                $seq = Db::select_value("
                    select seq
                    from pictures
                    where object_type = 1 and object_id = '".Db::escape_string($log_internal_id)."'
                    order by seq
                    limit ".($position+0).", 1
                ");
            }
        }

        # $position may have become a string, as returned by database queries.
        return array($position + 0, $seq, $log_images_count);
    }
}
