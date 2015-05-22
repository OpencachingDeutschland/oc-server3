<?php

namespace okapi\services\users\by_usernames;

use okapi\Okapi;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\services\caches\search\SearchAssistant;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;

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
        $usernames = $request->get_parameter('usernames');
        if (!$usernames) throw new ParamMissing('usernames');
        $usernames = explode("|", $usernames);
        if (count($usernames) > 500)
            throw new InvalidParam('usernames', "Maximum allowed number of referenced users ".
                "is 500. You provided ".count($usernames)." usernames.");
        $fields = $request->get_parameter('fields');
        if (!$fields)
            throw new ParamMissing('fields');

        # There's no need to validate the fields parameter as the 'users'
        # method does this (it will raise a proper exception on invalid values).

        $rs = Db::query("
            select username, uuid
            from user
            where username collate utf8_general_ci in ('".implode("','", array_map('mysql_real_escape_string', $usernames))."')
        ");
        $lower_username2useruuid = array();
        while ($row = mysql_fetch_assoc($rs))
        {
            $lower_username2useruuid[mb_strtolower($row['username'], 'utf-8')] = $row['uuid'];
        }
        mysql_free_result($rs);

        # Retrieve data for the found user_uuids.

        if (count($lower_username2useruuid) > 0)
        {
            $id_results = OkapiServiceRunner::call('services/users/users', new OkapiInternalRequest(
                $request->consumer, $request->token, array('user_uuids' => implode("|", array_values($lower_username2useruuid)),
                'fields' => $fields)));
        } else {
            $id_results = array();
        }

        # Map user_uuids back to usernames. Also check which usernames were not found
        # and mark them with null.

        $results = array();
        foreach ($usernames as $username)
        {
            if (!isset($lower_username2useruuid[mb_strtolower($username, 'utf-8')]))
                $results[$username] = null;
            else
                $results[$username] = $id_results[$lower_username2useruuid[mb_strtolower($username, 'utf-8')]];
        }

        return Okapi::formatted_response($request, $results);
    }
}
