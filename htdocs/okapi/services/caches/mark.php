<?php

namespace okapi\services\caches\mark;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiAccessToken;
use okapi\Settings;


class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 3
        );
    }

    public static function call(OkapiRequest $request)
    {
        # User is already verified (via OAuth), but we need to verify the
        # cache code (check if it exists). We will simply call a geocache method
        # on it - this will also throw a proper exception if it doesn't exist.

        $cache_code = $request->get_parameter('cache_code');
        if ($cache_code == null)
            throw new ParamMissing('cache_code');
        $geocache = OkapiServiceRunner::call('services/caches/geocache', new OkapiInternalRequest(
            $request->consumer, $request->token, array('cache_code' => $cache_code, 'fields' => 'internal_id')));

        # watched

        if ($tmp = $request->get_parameter('watched'))
        {
            if (!in_array($tmp, array('true', 'false', 'unchanged')))
                throw new InvalidParam('watched', $tmp);
            if ($tmp == 'true')
                Db::execute("
                    insert ignore into cache_watches (cache_id, user_id)
                    values (
                        '".Db::escape_string($geocache['internal_id'])."',
                        '".Db::escape_string($request->token->user_id)."'
                    );
                ");
            elseif ($tmp == 'false')
                Db::execute("
                    delete from cache_watches
                    where
                        cache_id = '".Db::escape_string($geocache['internal_id'])."'
                        and user_id = '".Db::escape_string($request->token->user_id)."';
                ");
        }

        # ignored

        if ($tmp = $request->get_parameter('ignored'))
        {
            if (!in_array($tmp, array('true', 'false', 'unchanged')))
                throw new InvalidParam('ignored', $tmp);
            if ($tmp == 'true')
                Db::execute("
                    insert ignore into cache_ignore (cache_id, user_id)
                    values (
                        '".Db::escape_string($geocache['internal_id'])."',
                        '".Db::escape_string($request->token->user_id)."'
                    );
                ");
            elseif ($tmp == 'false')
                Db::execute("
                    delete from cache_ignore
                    where
                        cache_id = '".Db::escape_string($geocache['internal_id'])."'
                        and user_id = '".Db::escape_string($request->token->user_id)."'
                ");
        }

        $result = array(
            'success' => true,
        );
        return Okapi::formatted_response($request, $result);
    }
}
