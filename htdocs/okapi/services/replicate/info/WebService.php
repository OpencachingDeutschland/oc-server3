<?php

namespace okapi\services\replicate\info;

use okapi\core\Cache;
use okapi\core\Okapi;
use okapi\core\Request\OkapiRequest;
use okapi\services\replicate\ReplicateCommon;

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
        $result = array();
        $result['changelog'] = array(
            'min_since' => ReplicateCommon::get_min_since(),
            'revision' => ReplicateCommon::get_revision(),
        );
        $dump = Cache::get("last_fulldump");
        if ($dump)
        {
            $result['latest_fulldump'] = array(
                'revision' => $dump['revision'],
                'generated_at' => $dump['meta']['generated_at'],
                'size' => $dump['meta']['compressed_size'],
                'size_uncompressed' => $dump['meta']['uncompressed_size'],
            );
        } else {
            $result['latest_fulldump'] = null;
        }

        return Okapi::formatted_response($request, $result);
    }
}
