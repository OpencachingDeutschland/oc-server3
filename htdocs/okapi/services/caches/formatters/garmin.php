<?php

namespace okapi\services\caches\formatters\garmin;


use okapi\Okapi;
use okapi\Cache;
use okapi\Settings;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiZIPHttpResponse;
use okapi\BadRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiAccessToken;
use okapi\services\caches\search\SearchAssistant;

use \Exception;
use \clsTbsZip;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 1
        );
    }

    public static function call(OkapiRequest $request)
    {
        $cache_codes = $request->get_parameter('cache_codes');
        if ($cache_codes === null) throw new ParamMissing('cache_codes');

        # Issue 106 requires us to allow empty list of cache codes to be passed into this method.
        # All of the queries below have to be ready for $cache_codes to be empty!

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";
        $langpref .= "|".Settings::get('SITELANG');
        $images = $request->get_parameter('images');
        if (!$images) $images = "all";
        if (!in_array($images, array("none", "all", "spoilers", "nonspoilers")))
            throw new InvalidParam('images');
        $format = $request->get_parameter('caches_format');
        if (!$format) $format = "gpx";
        if (!in_array($format, array("gpx", "ggz")))
            throw new InvalidParam('caches_format');

        $location_source = $request->get_parameter('location_source');
        $location_change_prefix = $request->get_parameter('location_change_prefix');

        # Start creating ZIP archive.
        $response = new OkapiZIPHttpResponse();

        # Include a GPX/GGZ file compatible with Garmin devices. It should include all
        # Geocaching.com (groundspeak:) and Opencaching.com (ox:) extensions. It will
        # also include image references (actual images will be added as separate files later)
        # and personal data (if the method was invoked using Level 3 Authentication).

        switch($format) {
            case 'gpx' :
                $data_filename = "Garmin/GPX/opencaching".time().rand(100000,999999).".gpx";
                $data_method = 'services/caches/formatters/gpx';
                $data_use_compression = true;
                break;
            case 'ggz' :
                $data_filename = "Garmin/GGZ/opencaching".time().rand(100000,999999).".ggz";
                $data_method = 'services/caches/formatters/ggz';
                $data_use_compression = false;
                break;
        }

        $response->zip->FileAdd($data_filename,
            OkapiServiceRunner::call($data_method, new OkapiInternalRequest(
            $request->consumer, $request->token, array(
                'cache_codes' => $cache_codes,
                'langpref' => $langpref,
                'ns_ground' => 'true',
                'ns_ox' => 'true',
                'images' => 'ox:all',
                'attrs' => 'ox:tags',
                'trackables' => 'desc:count',
                'alt_wpts' => 'true',
                'recommendations' => 'desc:count',
                'latest_logs' => 'true',
                'lpc' => 'all',
                'my_notes' => ($request->token != null) ? "desc:text" : "none",
                'location_source' => $location_source,
                'location_change_prefix' => $location_change_prefix
            )))->get_body(), clsTbsZip::TBSZIP_STRING, $data_use_compression);

        # Then, include all the images.

        $caches = OkapiServiceRunner::call('services/caches/geocaches', new OkapiInternalRequest(
            $request->consumer, $request->token, array('cache_codes' => $cache_codes,
            'langpref' => $langpref, 'fields' => "images")));
        if (count($caches) > 50)
            throw new InvalidParam('cache_codes', "The maximum number of caches allowed to be downloaded with this method is 50.");
        if ($images != 'none')
        {
            $supported_extensions = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
            foreach ($caches as $cache_code => $dict)
            {
                $imgs = $dict['images'];
                if (count($imgs) == 0)
                    continue;
                $dir = "Garmin/GeocachePhotos/".$cache_code[strlen($cache_code) - 1];
                $dir .= "/".$cache_code[strlen($cache_code) - 2];
                $dir .= "/".$cache_code;
                foreach ($imgs as $no => $img)
                {
                    if ($images == 'spoilers' && (!$img['is_spoiler']))
                        continue;
                    if ($images == 'nonspoilers' && $img['is_spoiler'])
                        continue;
                    $tmp = false;
                    foreach ($supported_extensions as $ext)
                    {
                        if (strtolower(substr($img['url'], strlen($img['url']) - strlen($ext) - 1)) != ".".$ext)
                        {
                            $tmp = true;
                            continue;
                        }
                    }
                    if (!$tmp)
                        continue;  # unsupported file extension

                    if ($img['is_spoiler']) {
                        $zippath = $dir."/Spoilers/".$img['unique_caption'].".jpg";
                    } else {
                        $zippath = $dir."/".$img['unique_caption'].".jpg";
                    }

                    # The safest way would be to use the URL, but that would be painfully slow!
                    # That's why we're trying to access files directly (and fail silently on error).
                    # This was tested on OCPL server only.

                    # Note: Oliver Dietz (oc.de) replied that images with 'local' set to 0 could not
                    # be accessed locally. But all the files have 'local' set to 1 anyway.

                    $syspath = Settings::get('IMAGES_DIR')."/".$img['uuid'].".jpg";
                    if (file_exists($syspath))
                    {
                        $response->zip->FileAdd($zippath, $syspath, clsTbsZip::TBSZIP_FILE, false);
                    }
                    else
                    {
                        # If file exists, but does not end with ".jpg", we will create
                        # JPEG version of it and store it in the cache.

                        $cache_key = "jpg#".$img['uuid'];
                        $jpeg_contents = Cache::get($cache_key);
                        if ($jpeg_contents === null)
                        {
                            foreach ($supported_extensions as $ext)
                            {
                                $syspath_other = Settings::get('IMAGES_DIR')."/".$img['uuid'].".".$ext;
                                if (file_exists($syspath_other))
                                {
                                    try
                                    {
                                        $image = imagecreatefromstring(file_get_contents($syspath_other));
                                        ob_start();
                                        imagejpeg($image);
                                        $jpeg_contents = ob_get_clean();
                                        imagedestroy($image);
                                    }
                                    catch (Exception $e)
                                    {
                                        # GD couldn't parse the file. We will skip it, and cache
                                        # the "false" value as the contents. This way, we won't
                                        # attempt to parse it during the next 24 hours.

                                        $jpeg_contents = false;
                                    }
                                    Cache::set($cache_key, $jpeg_contents, 86400);
                                    break;
                                }
                            }
                        }
                        if ($jpeg_contents)  # This can be "null" *or* "false"!
                            $response->zip->FileAdd($zippath, $jpeg_contents, clsTbsZip::TBSZIP_STRING, false);
                    }
                }
            }
        }

        # The result could be big, but it's created and streamed right
        # to the browser, so it shouldn't hit our memory limit. We also
        # should set a higher time limit, because downloading this response
        # may take some time over slow network connections (and I'm not sure
        # what is the PHP's default way of handling such scenario).

        set_time_limit(600);
        $response->content_type = "application/zip";
        $response->content_disposition = 'attachment; filename="results.zip"';
        return $response;
    }
}
