<?php

namespace okapi\services\caches\map\tile;

use okapi\Cache;
use okapi\Db;
use okapi\Exception\BadRequest;
use okapi\Exception\InvalidParam;
use okapi\Exception\ParamMissing;
use okapi\OkapiConsumer;
use okapi\OkapiServiceRunner;
use okapi\Request\OkapiInternalRequest;
use okapi\Request\OkapiRequest;
use okapi\Response\OkapiHttpResponse;
use okapi\services\caches\map\TileRenderer;
use okapi\services\caches\map\TileTree;
use okapi\Settings;

class WebService
{
    /**
     * Should be always true. You may temporarily set it to false, when you're
     * testing/debugging the tile renderer.
     */
    private static $USE_ETAGS_CACHE = true;

    /**
     * Should be always true. You may temporarily set it to false, when you're
     * testing/debugging the tile renderer.
     */
    private static $USE_IMAGE_CACHE = true;

    /**
     * Should be always true. You may temporarily set it to false, when you're
     * testing/debugging. Grep the code to check when this flag is used.
     */
    private static $USE_OTHER_CACHE = true;

    public static function options()
    {
        return array(
            'min_auth_level' => 3
        );
    }

    private static function require_uint($request, $name, $min_value = 0)
    {
        $val = $request->get_parameter($name);
        if ($val === null)
            throw new ParamMissing($name);
        $ret = intval($val);
        if ($ret < 0 || ("$ret" !== $val))
            throw new InvalidParam($name, "Expecting non-negative integer.");
        return $ret;
    }

    public static function call(OkapiRequest $request)
    {
        $checkpointA_started = microtime(true);

        # Make sure the request is internal.

        if (in_array($request->consumer->key, array('internal', 'facade'))) {
            /* Okay, these two consumers can always access it. */
        } elseif ($request->consumer->hasFlag(OkapiConsumer::FLAG_MAPTILE_ACCESS)) {
            /* If the Consumer is aware that it is not backward-compatible, then
             * he may be granted permission to access it. */
        } else {
            throw new BadRequest("Your Consumer Key has not been allowed to access this method.");
        }

        # zoom, x, y - required tile-specific parameters.

        $zoom = self::require_uint($request, 'z');
        if ($zoom > 21)
            throw new InvalidParam('z', "Maximum value for this parameter is 21.");
        $x = self::require_uint($request, 'x');
        $y = self::require_uint($request, 'y');
        if ($x >= 1<<$zoom)
            throw new InvalidParam('x', "Should be in 0..".((1<<$zoom) - 1).".");
        if ($y >= 1<<$zoom)
            throw new InvalidParam('y', "Should be in 0..".((1<<$zoom) - 1).".");

        # Now, we will create a search set (or use one previously created).
        # Instead of creating a new OkapiInternalRequest object, we will pass
        # the current request directly. We can do that, because we inherit all
        # of the "save" method's parameters.

        $search_set = OkapiServiceRunner::call(
            'services/caches/search/save',
            new OkapiInternalRequest(
                $request->consumer, $request->token,
                $request->get_all_parameters_including_unknown()
            )
        );
        $set_id = $search_set['set_id'];

        # Get caches which are present in the result set AND within the tile
        # (+ those around the borders).

        $rs = TileTree::query_fast($zoom, $x, $y, $set_id);
        $rows = array();
        if ($rs !== null)
        {
            while ($row = Db::fetch_row($rs))
                $rows[] = $row;
            unset($row);
        }
        OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointA", null,
            microtime(true) - $checkpointA_started);
        $checkpointB_started = microtime(true);

        # Add dynamic, user-related flags.

        if (count($rows) > 0)
        {
            # Load user-related cache ids.

            $cache_key = "tileuser/".$request->token->user_id;
            $user = self::$USE_OTHER_CACHE ? Cache::get($cache_key) : null;
            if ($user === null)
            {
                $user = array();

                # Ignored caches.

                $rs = Db::query("
                    select cache_id
                    from cache_ignore
                    where user_id = '".Db::escape_string($request->token->user_id)."'
                ");
                $user['ignored'] = array();
                while (list($cache_id) = Db::fetch_row($rs))
                    $user['ignored'][$cache_id] = true;

                # Found caches.

                $rs = Db::query("
                    select distinct cache_id
                    from cache_logs
                    where
                        user_id = '".Db::escape_string($request->token->user_id)."'
                        and type = 1
                        and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
                ");
                $user['found'] = array();
                while (list($cache_id) = Db::fetch_row($rs))
                    $user['found'][$cache_id] = true;

                # Own caches.

                $rs = Db::query("
                    select distinct cache_id
                    from caches
                    where user_id = '".Db::escape_string($request->token->user_id)."'
                ");
                $user['own'] = array();
                while (list($cache_id) = Db::fetch_row($rs))
                    $user['own'][$cache_id] = true;

                Cache::set($cache_key, $user, 30);
            }

            # Add extra flags to geocaches.

            foreach ($rows as &$row_ref)
            {
                # Add the "found" flag (to indicate that this cache needs
                # to be drawn as found) and the "own" flag (to indicate that
                # the current user is the owner).

                if (isset($user['found'][$row_ref[0]]))
                    $row_ref[6] |= TileTree::$FLAG_FOUND;  # $row[6] is "flags"
                if (isset($user['own'][$row_ref[0]]))
                    $row_ref[6] |= TileTree::$FLAG_OWN;  # $row[6] is "flags"
            }
        }

        # Compute the image hash/fingerprint. This will be used both for ETags
        # and internal cache ($cache_key).

        $tile = new TileRenderer($zoom, $rows);
        $image_fingerprint = $tile->get_unique_hash();

        # Start creating response.

        $response = new OkapiHttpResponse();
        $response->content_type = $tile->get_content_type();
        $response->cache_control = "Cache-Control: private, max-age=600";
        $response->etag = 'W/"'.$image_fingerprint.'"';
        $response->allow_gzip = false; // images are usually compressed, prevent compression at Apache level

        # Check if the request didn't include the same ETag.

        OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointB", null,
            microtime(true) - $checkpointB_started);
        $checkpointC_started = microtime(true);
        if (self::$USE_ETAGS_CACHE && ($request->etag == $response->etag))
        {
            # Hit. Report the content was unmodified.

            $response->etag = null;
            $response->status = "304 Not Modified";
            return $response;
        }

        # Check if the image was recently rendered and is kept in image cache.

        $cache_key = "tile/".$image_fingerprint;
        $response->body = self::$USE_IMAGE_CACHE ? Cache::get($cache_key) : null;
        OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointC", null,
            microtime(true) - $checkpointC_started);
        $checkpointD_started = microtime(true);
        if ($response->body !== null)
        {
            # Hit. We will use the cached version of the image.

            return $response;
        }

        # Miss. Render the image. Cache the result.

        $response->body = $tile->render();
        Cache::set_scored($cache_key, $response->body);
        OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointD", null,
            microtime(true) - $checkpointD_started);

        return $response;
    }
}
