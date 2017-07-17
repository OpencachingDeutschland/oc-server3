<?php

namespace okapi\services\replicate\changelog;

use okapi\BadRequest;
use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\ParamMissing;
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
        $since = $request->get_parameter('since');
        if ($since === null) throw new ParamMissing('since');
        if ((int)$since != $since) throw new InvalidParam('since');

        # Let's check the $since parameter.

        if (!ReplicateCommon::check_since_param($since))
            throw new BadRequest("The 'since' parameter is too old. You must update your database more frequently.");

        # Select a best chunk for the given $since, get the chunk from the database (or cache).

        list($from, $to) = ReplicateCommon::select_best_chunk($since);
        $clog_entries = ReplicateCommon::get_chunk($from, $to);

        $result = array(
            'changelog' => &$clog_entries,
            'revision' => $to + 0,
            'more' => $to < ReplicateCommon::get_revision(),
        );

        return Okapi::formatted_response($request, $result);
    }
}
