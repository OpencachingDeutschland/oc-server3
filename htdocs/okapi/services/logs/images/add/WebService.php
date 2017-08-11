<?php

namespace okapi\services\logs\images\add;

use Exception;
use okapi\Db;
use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\services\logs\images\LogImagesCommon;
use okapi\Settings;


/**
 * This exception is thrown by WebService::_call method, when error is detected in
 * user-supplied data. It is not a BadRequest exception - it does not imply that
 * the Consumer did anything wrong (it's the user who did). This exception shouldn't
 * be used outside of this file.
 */
class CannotPublishException extends Exception {}

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 3
        );
    }


    /**
     * Append a new image to a log entry and return the image uuid and position.
     * Throws CannotPublishException or BadRequest on errors.
     */

    private static function _call(OkapiRequest $request)
    {
        # Developers! Please notice the fundamental difference between throwing
        # CannotPublishException and the "standard" BadRequest/InvalidParam
        # exceptions. You're reading the "_call" method now (see below for
        # "call").

        # validate the 'log_uuid' parameter

        $log_uuid = $request->get_parameter('log_uuid');
        if (!$log_uuid)
            throw new ParamMissing('log_uuid');
        $rs = Db::query("
            select id, node, user_id
            from cache_logs
            where uuid = '".Db::escape_string($log_uuid)."'"
        );
        $row = Db::fetch_assoc($rs);
        Db::free_result($rs);
        if (!$row)
            throw new InvalidParam('log_uuid', "There is no log entry with uuid '".$log_uuid."'.");
        if ($row['node'] != Settings::get('OC_NODE_ID')) {
            throw new Exception(
                "This site's database contains the log entry '$log_uuid' which has been"
                . " imported from another OC node. OKAPI is not prepared for that."
            );
        }
        if ($row['user_id'] != $request->token->user_id) {
            throw new InvalidParam(
                'log_uuid',
                "The user of your access token is not the log entry's author."
            );
        }
        $log_internal_id = $row['id'];
        unset($row);

        # validate the 'caption', 'is_spoiler' and 'position' parameters

        $caption = $request->get_parameter('caption');
        if (!$caption) {
            throw new CannotPublishException(sprintf(
                _("%s requires all images to have captions. Please provide one."),
                Okapi::get_normalized_site_name()
            ));
        }

        $is_spoiler = $request->get_parameter('is_spoiler');
        if ($is_spoiler === null) $is_spoiler = 'false';
        if (!in_array($is_spoiler, array('true', 'false')))
            throw new InvalidParam('is_spoiler');

        $position = LogImagesCommon::validate_position($request);

        # validate the 'image' parameter

        $base64_image = $request->get_parameter('image');
        if (!$base64_image)
            throw new ParamMissing('image');

        $estimated_decoded_size = strlen($base64_image) / 4 * 3 - 2;
        if ($estimated_decoded_size > Settings::get('IMAGE_MAX_UPLOAD_SIZE'))
        {
            $estimated_decoded_size_MB = round($estimated_decoded_size / 1024 / 1024, 1);
            $max_upload_size_MB = round(Settings::get('IMAGE_MAX_UPLOAD_SIZE') / 1024 / 1024, 1);

            throw new CannotPublishException(sprintf(
                _("Your image file is too large (%s.%s MB); %s accepts a maximum image size of %s.%s MB."),
                floor($estimated_decoded_size_MB), ($estimated_decoded_size_MB * 10) % 10,
                Okapi::get_normalized_site_name(),
                floor($max_upload_size_MB), ($max_upload_size_MB * 10) % 10
            ));
        }

        $image = base64_decode($base64_image);
        if (!$image)
            throw new InvalidParam('image', "bad base64 encoding");

        try {
            $image_properties = getimagesizefromstring($image);  # can throw
            if (!$image_properties)
                throw new Exception();
            list($width, $height, $image_type) = $image_properties;
            if (!in_array($image_type, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF))) {
                # This will happen e.g. for BMP and XBM images, which are supported by GD.
                throw new Exception();
            }
        }
        catch (Exception $e) {
            # Note: There may be *subtypes* which are not accepted by the GD library.
            # About 1 of 2000 JPEGs at OC.de is not readable by the PHP functions,
            # though they can be displayed by web browsers.

            throw new CannotPublishException(
                sprintf(
                    _("%s reported an error when it tried to read your file."),
                    Okapi::get_normalized_site_name()
                ).
                " ".
                _("Make sure you are sending a file in an appropriate format (JPEG, PNG or GIF).")
            );
        }
        unset($image_properties);

        if ($width * $height > self::max_pixels($base64_image)) {
            # This large image may crash the image processing functions.

            throw new CannotPublishException(sprintf(_(
                "The image you have uploaded is too large (%s megapixels). ".
                "Please use an external application to downscale it first. ".
                "(You can also contact the developers of '%s' and ask them to ".
                "downscale uploaded images automatically.)"
            ), round($width * $height / 1024 / 1024), $request->consumer->name));
        }
        try {
            $image = imagecreatefromstring($image);  # can throw
            if (!$image) throw new Exception();
        }
        catch (Exception $e) {
            throw new CannotPublishException(sprintf(
                _("%s reported an error when it tried to read your file."),
                Okapi::get_normalized_site_name()
            ));
        }

        # Now all supplied paramters are validated.

        # Do any postprocessing like scaling and rotating
        $image = self::postprocess_image($base64_image, $image, $image_type, $width, $height);
        unset($base64_image);

        # Save the image file. By saving it always from the $image object instead of
        # the original image data (even if not downscaled or rotated), we
        #   - strip JPEG EXIF information, which is intentional for privacy reasons,
        #   - eliminate any data flaws which have may been in the source files.

        $image_uuid = Okapi::create_uuid();
        $imagepath = Settings::get('IMAGES_DIR').'/'.$image_uuid;
        switch ($image_type)
        {
            case IMAGETYPE_JPEG:
                $file_ext = '.jpg';
                $quality = Settings::get('JPEG_QUALITY');
                $result = imagejpeg($image, $imagepath.$file_ext, $quality);
                break;

            case IMAGETYPE_PNG:
                $file_ext = '.png';
                $result = imagepng($image, $imagepath.$file_ext);
                break;

            case IMAGETYPE_GIF:
                $file_ext = '.gif';
                $result = imagegif($image, $imagepath.$file_ext);
                break;

            default:
                $file_ext = '.???';
                $result = false;
        }
        if (!$result)
            throw new Exception("could not save image file '".$imagepath.$file_ext."'");

        # insert image into database

        try
        {
            $position = self::db_insert_image(
                $request->consumer->key, $request->token->user_id,
                $log_internal_id, $image_uuid, $position, $caption, $is_spoiler, $file_ext
            );
        }
        catch (Exception $e)
        {
            # TODO: Proper handling of nested exception if the unlink() fails
            # (which is very unlikely, and will just add a bit more of garbage
            # to that which is already present in the images directory).

            try { unlink($imagepath.$file_ext); }
            catch (Exception $e2) {}
            throw $e;
        }

        return array($image_uuid, $position);
    }

    /**
     * Estimate an upper limit of the processable image dimensions.
     * This will be in the scale of ~40 MP for 256 MB memory_limit and
     * ~16 MP for 128 MB. See Okapi::init_internals() for current memory_limit.
     */

    private static function max_pixels($base64_image)
    {
        $bytes_per_pixel = 5;   # GD needs 5 bytes per pixel for "true color"
        $available_memory = Okapi::from_human_to_bytes(ini_get('memory_limit')) - memory_get_usage();
        $available_memory -= 16 * 1024 * 1024;  # reserve
        $available_memory -= strlen($base64_image);  # will be copied for EXIF processing
        $available_memory -= 3 * $bytes_per_pixel * Settings::get('IMAGE_MAX_PIXEL_COUNT');  # processing buffers
        return floor($available_memory / $bytes_per_pixel);
    }

    private static function postprocess_image($base64_image, $image, $image_type, $width, $height)
    {
        # We use the GD library for image processing, which is available by
        # default in all modern PHP installations. Imagick would be a nice
        # and more powerful alternative (e.g. allowing more file types),
        # but that needs additional and sometimes non-trivial installation.

        if (!extension_loaded('gd'))
            throw new Exception('PHP GD image processing module is disabled');

        # rescale image if necessary

        $scale_factor = sqrt(Settings::get('IMAGE_MAX_PIXEL_COUNT') / ($width * $height));

        if ($scale_factor < 1)
        {
            $new_width = $width * $scale_factor;
            $new_height = $height * $scale_factor;
            $scaled_image = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($scaled_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $image = $scaled_image;
            unset($scaled_image);
        }

        # Resolve EXIF rotation, because
        #   - we strip EXIF data for privacy reasons,
        #   - GD cannot save EXIF data anyway, and
        #   - the orientation flag may not be recognized by all image processors.

        if (!extension_loaded('exif'))
            throw new Exception('PHP Exif module is disabled');

        if ($image_type == IMAGETYPE_JPEG)
        {
            # The PHP Exif module can read EXIF data only from files. To avoid
            # disk I/O overhead, we pipe the image string through a pseudo-file:

            $exif_data = exif_read_data("data://image/jpeg;base64," . $base64_image);
            if ($exif_data && isset($exif_data['Orientation'])) {
                switch ($exif_data['Orientation'])
                {
                    case 3: $image = imagerotate($image, 180, 0); break;
                    case 6: $image = imagerotate($image, -90, 0); break;
                    case 8: $image = imagerotate($image, 90, 0); break;
                }
            }
        }

        return $image;
    }


    private static function db_insert_image(
        $consumer_key, $user_id,
        $log_internal_id, $image_uuid, $position, $caption, $is_spoiler, $file_ext)
    {
        list($position, $seq, $log_images_count) = LogImagesCommon::prepare_position(
            $log_internal_id,
            $position,
            +1  # if appended at the end of list, use the last image's pos./seq + 1
        );
        # For OCDE the pictures table is write locked now.

        # Transactions do not work on OCDE MyISAM tables. However, the worst
        # thing that can happen on OCDE is creating a sequence number gap,
        # which is allowed.
        #
        # For OCPL InnoDB tables, the transactions DO and MUST work, because
        # we write to two dependent tables.

        Db::execute('start transaction');

        # shift positions of existing images to make space for the new one
        if ($position < $log_images_count && Settings::get('OC_BRANCH') == 'oc.de') {
            Db::execute("
                update pictures
                set seq = seq + 1
                where
                    object_type = 1
                    and object_id = '".Db::escape_string($log_internal_id)."'
                    and seq >= '".Db::escape_string($seq)."'
                order by seq desc
            ");
        }

        if (Settings::get('OC_BRANCH') == 'oc.de') {
            $local_fields_SQL = "seq";
            $local_values_escaped_SQL = "'".Db::escape_string($seq)."'";
            # All other fields are set by trigger or defaults for OCDE.
        } else {
            # These are the additional fields that OCPL newpic.php supplies
            # (seq is set from default):
            $local_fields_SQL =
                "date_created, last_modified, description, desc_html, last_url_check, user_id";
            $local_values_escaped_SQL =
                "NOW(), NOW(), '', 0, NOW(), '".Db::escape_string($user_id)."'";
        }

        Db::execute("
            insert into pictures (
                uuid, node, local, title, spoiler, url, object_type, object_id,
                unknown_format, display,
                ".$local_fields_SQL."
            )
            values (
                '".Db::escape_string($image_uuid)."',
                '".Db::escape_string(Settings::get('OC_NODE_ID'))."',
                1,
                '".Db::escape_string($caption)."',
                '".($is_spoiler == 'true' ? 1 : 0)."',
                '".Db::escape_string(Settings::get('IMAGES_URL').$image_uuid.$file_ext)."',
                1,
                '".Db::escape_string($log_internal_id)."',
                0,
                1,
                ".$local_values_escaped_SQL."
            )
        ");
        $image_internal_id = Db::last_insert_id();

        # update OCPL log entry properties; OCDE does everything necessary by triggers

        if (Settings::get('OC_BRANCH') == 'oc.pl') {
            # This will also update cache_logs.okapi_syncbase, so that replication
            # can output the updated log entry with one image less. For OCDE
            # that's done by DB triggers.

            Db::execute("
                update cache_logs
                set
                    picturescount = picturescount + 1,
                    last_modified = NOW()
                where id = '".Db::escape_string($log_internal_id)."'
            ");
        }

        # Store information on the consumer_key which uploaded this image.
        # (Maybe we'll want to display this somewhen later.)

        Db::execute("
            insert into okapi_submitted_objects (object_type, object_id, consumer_key)
            values (
                '".Okapi::OBJECT_TYPE_CACHE_LOG_IMAGE."',
                '".Db::escape_string($image_internal_id)."',
                '".Db::escape_string($consumer_key)."'
            );
        ");

        Db::execute('commit');
        Db::execute('unlock tables');

        return $position;
    }


    public static function call(OkapiRequest $request)
    {
        # This is the "real" entry point. A wrapper for the _call method.

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";
        $langprefs = explode("|", $langpref);
        Okapi::gettext_domain_init($langprefs);

        try
        {
            list($image_uuid, $position) = self::_call($request);
            $result = array(
                'success' => true,
                'message' => _("Image has been successfully saved."),
                'image_uuid' => $image_uuid,
                'position' => $position
            );
            Okapi::gettext_domain_restore();
        }
        catch (CannotPublishException $e)
        {
            Okapi::gettext_domain_restore();
            $result = array(
                'success' => false,
                'message' => $e->getMessage(),
                'image_uuid' => null,
                'position' => null
            );
        }

        Okapi::update_user_activity($request);
        return Okapi::formatted_response($request, $result);
    }
}
