<?php

namespace okapi;

/**
 * This is a (hopefully temporary) class which holds all funtionality related
 * to OCPL's "access logging" feature. OCPL admins use this feature to track
 * suspicious activity of some certain users.
 *
 * Maintainer: boguslaw.szczepanowski@gmail.com
 */
class OCPLAccessLogs
{
    /**
     * Return method name (without full path) which was originally called against OKAPI.
     */
    public static function get_original_caller()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $org_caller = null;
        $break_next = false;
        // traverse PHP call stack to find out who originally called us
        // first, find first service_runner.php invocation
        // then, find previous class invocation
        for($i = count($trace)-1; $i >= 0; $i--)
        {
            $frame = $trace[$i];
            if ($break_next && isset($frame['class']))
            {
                $class_elems = explode('\\', $frame['class']);
                if (count($class_elems) >= 2)
                    $org_caller = $class_elems[count($class_elems)-2];
                break;
            }
            if (isset($frame['file']) &&
                    // test if file ends with service_runner.php
                    substr($frame['file'], -strlen('service_runner.php')) === 'service_runner.php')
            {
                $break_next = true;
            }
        }
        return $org_caller;
    }

    /**
     * Log detailed geocache data access
     * @param OkapiRequest $request
     * @param mixed $cache_ids An index based array of geocache ids, or a single geocache id.
     *                 The parameter MUST contain only valid, non duplicated geocache ids.
     */
    public static function log_geocache_access(OkapiRequest $request, $cache_ids)
    {
        if (Settings::get('OCPL_ENABLE_GEOCACHE_ACCESS_LOGS') !== true)
            return ;

        if (Settings::get('OC_BRANCH') == 'oc.pl')
        {
            // TODO: can we use the _SERVER global here? or should we make them abstract, and
            // pass along with request object?
            $remote_addr_escaped = "'" . Db::escape_string($_SERVER['REMOTE_ADDR']) . "'";
            $user_agent_escaped = isset($_SERVER['HTTP_USER_AGENT']) ?
                "'" . Db::escape_string($_SERVER['HTTP_USER_AGENT']) . "'" : "null";
            $forwarded_for_escaped = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?
                "'" . Db::escape_string($_SERVER['HTTP_X_FORWARDED_FOR']) . "'" : "null";

            $consumer_key_escaped = "'" . Db::escape_string($request->consumer->key) . "'";
            $original_caller_escaped = "'" . Db::escape_string(self::get_original_caller()) . "'";

            $user_id = null;
            if ($request->token != null)
                $user_id = $request->token->user_id;
            $user_id_escaped = $user_id === null ? "null" : "'" . Db::escape_string($user_id) . "'";
            if (is_array($cache_ids)){
                if (count($cache_ids) == 1)
                    $cache_ids_where = "= '" . Db::escape_string($cache_ids[0]) . "'";
                else
                    $cache_ids_where = "in ('" . implode("','", array_map('\okapi\Db::escape_string', $cache_ids)) . "')";
            } else {
                $cache_ids_where = "= '" . Db::escape_string($cache_ids) . "'";
            }

            $sql = "
                select cache_id
                from CACHE_ACCESS_LOGS cal
                where
                    cache_id $cache_ids_where" .
                    ($user_id === null ? " and cal.user_id is null" : " and cal.user_id = $user_id_escaped") . "
                    and cal.source = 'O'
                    and cal.event = $original_caller_escaped
                    and cal.okapi_consumer_key = $consumer_key_escaped
                    and date_sub(now(), interval 1 hour) < cal.event_date ";
            if ($user_id === null) {
                $sql .= " and cal.ip_addr = $remote_addr_escaped ";
                $sql .= isset($_SERVER['HTTP_USER_AGENT']) ? " and cal.user_agent = $user_agent_escaped " : " and cal.user_agent is null ";
            }
            $already_logged_cache_ids = Db::select_column($sql);
            unset($cache_ids_where);
            unset($sql);

            // check, if all the geocaches has already been logged
            if (is_array($cache_ids) && count($already_logged_cache_ids) == count($cache_ids)
                || !is_array($cache_ids) && count($already_logged_cache_ids) == 1)
            {
                return ;
            }

            if (is_array($cache_ids)){
                $tmp = array();
                foreach ($cache_ids as $cache_id)
                    $tmp[$cache_id] = true;
                foreach ($already_logged_cache_ids as $cache_id)
                    unset($tmp[$cache_id]);
                if (count($tmp) <= 0)
                    return ;
                $cache_ids_filterd = array_keys($tmp);
                unset($tmp);
            } else {
                $cache_ids_filterd = $cache_ids;
            }

            if (is_array($cache_ids_filterd)){
                if (count($cache_ids_filterd) == 1)
                    $cache_ids_where = "= '" . Db::escape_string($cache_ids_filterd[0]) . "'";
                else
                    $cache_ids_where = "in ('" . implode("','", array_map('\okapi\Db::escape_string', $cache_ids_filterd)) . "')";
            } else {
                $cache_ids_where = "= '" . Db::escape_string($cache_ids_filterd) . "'";
            }

            Db::execute("
                insert into CACHE_ACCESS_LOGS (event_date, cache_id, user_id, source, event, ip_addr,
                    user_agent, forwarded_for, okapi_consumer_key)
                select
                    now(), cache_id, $user_id_escaped, 'O',
                    $original_caller_escaped, $remote_addr_escaped, $user_agent_escaped, $forwarded_for_escaped,
                    $consumer_key_escaped
                from caches
                where cache_id $cache_ids_where
            ");
        }
    }
}