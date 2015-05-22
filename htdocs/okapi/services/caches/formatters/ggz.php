<?php

namespace okapi\services\caches\formatters\ggz;

use okapi\Okapi;
use okapi\Cache;
use okapi\Settings;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\BadRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiAccessToken;
use okapi\OkapiZIPHttpResponse;
use okapi\services\caches\search\SearchAssistant;

use \ZipArchive;
use \Exception;

require_once($GLOBALS['rootpath']."okapi/services/caches/formatters/gpx.php");

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
        $gpx_result = \okapi\services\caches\formatters\gpx\WebService::create_gpx(
                $request,
                \okapi\services\caches\formatters\gpx\WebService::FLAG_CREATE_GGZ_IDX
        );

        $response = new OkapiZIPHttpResponse();

        # Include a GPX file compatible with Garmin devices. It should include all
        # Geocaching.com (groundspeak:) and Opencaching.com (ox:) extensions. It will
        # also include personal data (if the method was invoked using Level 3 Authentication).

        $file_item_name = "data_".time()."_".rand(100000,999999).".gpx";
        $ggz_file = array(
            'name' => $file_item_name,
            'crc32' => sprintf('%08X', crc32($gpx_result['gpx'])),
            'caches' => $gpx_result['ggz_entries']
        );

        $vars = array();
        $vars['files'] = array($ggz_file);

        ob_start();
        include 'ggzindex.tpl.php';
        $index_content = ob_get_clean();

        $response->zip->FileAdd("index/com/garmin/geocaches/v0/index.xml", $index_content);
        $response->zip->FileAdd("data/".$file_item_name, $gpx_result['gpx']);

        unset($gpx_result);
        unset($index_content);

        $response->content_type = "application/x-ggz";
        $response->content_disposition = 'attachment; filename="geocaches.ggz"';
        return $response;
    }
}
