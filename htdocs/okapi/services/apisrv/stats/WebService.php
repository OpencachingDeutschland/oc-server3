<?php

namespace okapi\services\apisrv\stats;

use okapi\core\Cache;
use okapi\core\Db;
use okapi\core\Okapi;
use okapi\core\Request\OkapiRequest;
use okapi\Settings;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 0
        );
    }

    public static function call(OkapiRequest $request)
    {
        $cachekey = "apisrv/stats";
        $result = Cache::get($cachekey);
        if (!$result)
        {
            $result = array(
                'cache_count' => 0 + Db::select_value("
                    select count(*) from caches where status in (1,2,3)
                "),
                'user_count' => 0 + Db::select_value("
                    select count(*) from (
                        select distinct user_id
                        from cache_logs
                        where
                            type in (1,2,7)
                            and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
                        UNION DISTINCT
                        select distinct user_id
                        from caches
                    ) as t;
                "),
                'apps_count' => 0 + Db::select_value("select count(*) from okapi_consumers;"),
                'apps_active' => 0 + Db::select_value("
                    select count(distinct s.consumer_key)
                    from
                        okapi_stats_hourly s,
                        okapi_consumers c
                    where
                        s.consumer_key = c.`key`
                        and s.period_start > date_add(now(), interval -30 day)
                "),
            );
            Cache::set($cachekey, $result, 86400); # cache it for one day
        }
        return Okapi::formatted_response($request, $result);
    }
}
