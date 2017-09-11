<?php

namespace okapi\services\caches\formatters\ggz;

use okapi\core\Request\OkapiRequest;
use okapi\core\Response\OkapiZIPHttpResponse;
use okapi\services\caches\formatters\gpx\WebService as GpxWebService;

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
        $gpx_result = GpxWebService::create_gpx(
                $request,
                GpxWebService::FLAG_CREATE_GGZ_IDX
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
        include __DIR__ . '/ggzindex.tpl.php';
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
