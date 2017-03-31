<?php

namespace okapi\services\replicate\fulldump;

use okapi\BadRequest;
use okapi\Cache;
use okapi\Db;
use okapi\OkapiHttpResponse;
use okapi\OkapiRequest;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 1
        );
    }

    private static function count_calls($consumer_key, $days)
    {
        return (
            Db::select_value("
                select count(*)
                from okapi_stats_temp
                where
                    consumer_key = '".Db::escape_string($consumer_key)."'
                    and service_name='services/replicate/fulldump'
            ")
            +
            Db::select_value("
                select sum(total_calls)
                from okapi_stats_hourly
                where
                    consumer_key = '".Db::escape_string($consumer_key)."'
                    and service_name='services/replicate/fulldump'
                    and period_start > date_add(now(), interval -$days day)
                limit 1
            ")
        );
    }

    public static function call(OkapiRequest $request)
    {
        require_once('replicate_common.inc.php');

        $data = Cache::get("last_fulldump");
        if ($data == null)
            throw new BadRequest("No fulldump found. Try again later. If this doesn't help ".
                "contact site administrator and/or OKAPI developers.");

        # Check consumer's quota

        $please = $request->get_parameter('pleeaase');
        if ($please != 'true')
        {
            $not_good = 3 < self::count_calls($request->consumer->key, 30);
            if ($not_good)
                throw new BadRequest("Consumer's monthly quota exceeded. Try later or call with '&pleeaase=true'.");
        }
        else
        {
            $not_good = 5 < self::count_calls($request->consumer->key, 1);
            if ($not_good)
                throw new BadRequest("No more please. Seriously, dude...");
        }

        $response = new OkapiHttpResponse();
        $response->content_type = $data['meta']['content_type'];
        $response->content_disposition = 'attachment; filename="'.$data['meta']['public_filename'].'"';
        $response->stream_length = $data['meta']['compressed_size'];
        $response->body = fopen($data['meta']['filepath'], "rb");
        $response->allow_gzip = false;
        return $response;
    }
}
