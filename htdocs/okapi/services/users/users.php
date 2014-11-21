<?php

namespace okapi\services\users\users;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\services\caches\search\SearchAssistant;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 1
        );
    }

    private static $valid_field_names = array('uuid', 'username', 'profile_url', 'internal_id', 'is_admin',
        'caches_found', 'caches_notfound', 'caches_hidden', 'rcmds_given');

    public static function call(OkapiRequest $request)
    {
        $user_uuids = $request->get_parameter('user_uuids');
        if (!$user_uuids) throw new ParamMissing('user_uuids');
        $user_uuids = explode("|", $user_uuids);
        if (count($user_uuids) > 500)
            throw new InvalidParam('user_uuids', "Maximum allowed number of referenced users ".
                "is 500. You provided ".count($user_uuids)." user IDs.");
        $fields = $request->get_parameter('fields');
        if (!$fields)
            throw new ParamMissing('fields');
        $fields = explode("|", $fields);
        foreach ($fields as $field)
            if (!in_array($field, self::$valid_field_names))
                throw new InvalidParam('fields', "'$field' is not a valid field code.");
        $rs = Db::query("
            select user_id, uuid, username, admin
            from user
            where uuid in ('".implode("','", array_map('mysql_real_escape_string', $user_uuids))."')
        ");
        $results = array();
        $id2uuid = array();
        $uuid2id = array();
        while ($row = mysql_fetch_assoc($rs))
        {
            $id2uuid[$row['user_id']] = $row['uuid'];
            $uuid2id[$row['uuid']] = $row['user_id'];
            $entry = array();
            foreach ($fields as $field)
            {
                switch ($field)
                {
                    case 'uuid': $entry['uuid'] = $row['uuid']; break;
                    case 'username': $entry['username'] = $row['username']; break;
                    case 'profile_url': $entry['profile_url'] = Settings::get('SITE_URL')."viewprofile.php?userid=".$row['user_id']; break;
                    case 'is_admin':
                        if (!$request->token) {
                            $entry['is_admin'] = null;
                        } elseif ($request->token->user_id != $row['user_id']) {
                            $entry['is_admin'] = null;
                        } else {
                            $entry['is_admin'] = $row['admin'] ? true : false;
                        }
                        break;
                    case 'internal_id': $entry['internal_id'] = $row['user_id']; break;
                    case 'caches_found': /* handled separately */ break;
                    case 'caches_notfound': /* handled separately */ break;
                    case 'caches_hidden': /* handled separately */ break;
                    case 'rcmds_given': /* handled separately */ break;
                    default: throw new Exception("Missing field case: ".$field);
                }
            }
            $results[$row['uuid']] = $entry;
        }
        mysql_free_result($rs);

        # caches_found, caches_notfound, caches_hidden

        if (in_array('caches_found', $fields) || in_array('caches_notfound', $fields) || in_array('caches_hidden', $fields)
            || in_array('rcmds_given', $fields))
        {
            # We will load all these stats together. Then we may remove these which
            # the user doesn't need.

            $extras = array();

            if (Settings::get('OC_BRANCH') == 'oc.pl')
            {
                # OCPL stores user stats in 'user' table.

                $rs = Db::query("
                    select user_id, founds_count, notfounds_count, hidden_count
                    from user
                    where user_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($id2uuid)))."')
                ");
            }
            else
            {
                # OCDE stores user stats in 'stat_user' table.

                $rs = Db::query("
                    select
                        u.user_id,
                        ifnull(su.found, 0) as founds_count,
                        ifnull(su.notfound, 0) as notfounds_count,
                        ifnull(su.hidden, 0) as hidden_count
                    from
                        user u
                        left join stat_user su
                            on su.user_id = u.user_id
                    where u.user_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($id2uuid)))."')
                ");
            }

            while ($row = mysql_fetch_assoc($rs))
            {
                $extras[$row['user_id']] = array();;
                $extra_ref = &$extras[$row['user_id']];
                $extra_ref['caches_found'] = 0 + $row['founds_count'];
                $extra_ref['caches_notfound'] = 0 + $row['notfounds_count'];
                $extra_ref['caches_hidden'] = 0 + $row['hidden_count'];
            }
            mysql_free_result($rs);

            if (in_array('rcmds_given', $fields))
            {
                $rs = Db::query("
                    select user_id, count(*) as rcmds_given
                    from cache_rating
                    where user_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($id2uuid)))."')
                    group by user_id
                ");
                $rcmds_counts = array();
                while ($row = mysql_fetch_assoc($rs))
                    $rcmds_counts[$row['user_id']] = $row['rcmds_given'];
                foreach ($extras as $user_id => &$extra_ref)
                {
                    $extra_ref['rcmds_given'] = isset($rcmds_counts[$user_id]) ? 0 + $rcmds_counts[$user_id] : 0;
                }
            }

            # "Apply" only those fields which the consumer wanted.

            foreach (array('caches_found', 'caches_notfound', 'caches_hidden', 'rcmds_given') as $field)
            {
                if (!in_array($field, $fields))
                    continue;
                foreach ($results as $uuid => &$result_ref)
                    $result_ref[$field] = $extras[$uuid2id[$uuid]][$field];
            }
        }

        # Check which user IDs were not found and mark them with null.

        foreach ($user_uuids as $user_uuid)
            if (!isset($results[$user_uuid]))
                $results[$user_uuid] = null;

        return Okapi::formatted_response($request, $results);
    }
}
