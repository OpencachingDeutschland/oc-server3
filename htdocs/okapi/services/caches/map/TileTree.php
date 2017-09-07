<?php

namespace okapi\services\caches\map;

use Exception;
use okapi\Consumer\OkapiInternalConsumer;
use okapi\Db;
use okapi\Okapi;
use okapi\OkapiServiceRunner;
use okapi\Request\OkapiInternalRequest;


class TileTree
{
    # Static flags (stored in the database).
    public static $FLAG_STAR = 0x01;
    public static $FLAG_HAS_TRACKABLES = 0x02;
    public static $FLAG_NOT_YET_FOUND = 0x04;

    # Dynamic flags (added at runtime).
    public static $FLAG_FOUND = 0x0100;
    public static $FLAG_OWN = 0x0200;
    public static $FLAG_NEW = 0x0400;
    public static $FLAG_DRAW_CAPTION = 0x0800;

    /**
     * Return null if not computed, 1 if computed and empty, 2 if computed and not empty.
     */
    public static function get_tile_status($zoom, $x, $y)
    {
        return Db::select_value("
            select status
            from okapi_tile_status
            where
                z = '".Db::escape_string($zoom)."'
                and x = '".Db::escape_string($x)."'
                and y = '".Db::escape_string($y)."'
        ");
    }

    /**
     * Return MySQL's result set iterator over all caches which are present
     * in the given result set AND in the given tile.
     *
     * Each row is an array of the following format:
     * list(cache_id, $pixel_x, $pixel_y, status, type, rating, flags, name_crc, [count]).
     *
     * Note that $pixels can also be negative or >=256 (up to a margin of 32px).
     * Count is the number of other caches "eclipsed" by this geocache (such
     * eclipsed geocaches are not included in the result).
     */
    public static function query_fast($zoom, $x, $y, $set_id)
    {
        # First, we check if the cache-set for this tile was already computed
        # (and if it was, was it empty).

        $status = self::get_tile_status($zoom, $x, $y);
        if ($status === null)  # Not yet computed.
        {
            # Note, that computing the tile does not involve taking any
            # search parameters.

            $status = self::compute_tile($zoom, $x, $y);
        }

        if ($status === 1)  # Computed and empty.
        {
            # This tile was already computed and it is empty.
            return null;
        }

        # If we got here, then the tile is computed and not empty (status 2).

        $tile_upper_x = $x << 8;
        $tile_leftmost_y = $y << 8;

        $zoom_escaped = "'".Db::escape_string($zoom)."'";
        $tile_upper_x_escaped = "'".Db::escape_string($tile_upper_x)."'";
        $tile_leftmost_y_escaped = "'".Db::escape_string($tile_leftmost_y)."'";
        return Db::query("
            select
                otc.cache_id,
                cast(otc.z21x >> (21 - $zoom_escaped) as signed) - $tile_upper_x_escaped as px,
                cast(otc.z21y >> (21 - $zoom_escaped) as signed) - $tile_leftmost_y_escaped as py,
                otc.status, otc.type, otc.rating, otc.flags, otc.name_crc, count(*)
            from
                okapi_tile_caches otc,
                okapi_search_results osr
            where
                z = $zoom_escaped
                and x = '".Db::escape_string($x)."'
                and y = '".Db::escape_string($y)."'
                and otc.cache_id = osr.cache_id
                and osr.set_id = '".Db::escape_string($set_id)."'
            group by
                z21x >> (3 + (21 - $zoom_escaped)),
                z21y >> (3 + (21 - $zoom_escaped))
            order by
                z21y >> (3 + (21 - $zoom_escaped)),
                z21x >> (3 + (21 - $zoom_escaped))
        ");
    }

    /**
     * Precache the ($zoom, $x, $y) slot in the okapi_tile_caches table.
     */
    public static function compute_tile($zoom, $x, $y)
    {
        $time_started = microtime(true);

        # Note, that multiple threads may try to compute tiles simulatanously.
        # For low-level tiles, this can be expensive.

        $status = self::get_tile_status($zoom, $x, $y);
        if ($status !== null)
            return $status;

        if ($zoom === 0)
        {
            # When computing zoom zero, we don't have a parent to speed up
            # the computation. We need to use the caches table. Note, that
            # zoom level 0 contains *entire world*, so we don't have to use
            # any WHERE condition in the following query.

            # This can be done a little faster (without the use of internal requests),
            # but there is *no need* to - this query is run seldom and is cached.

            $params = array();
            $params['status'] = "Available|Temporarily unavailable|Archived";  # we want them all
            $params['limit'] = "10000000";  # no limit

            $internal_request = new OkapiInternalRequest(new OkapiInternalConsumer(), null, $params);
            $internal_request->skip_limits = true;
            $response = OkapiServiceRunner::call("services/caches/search/all", $internal_request);
            $cache_codes = $response['results'];

            $internal_request = new OkapiInternalRequest(new OkapiInternalConsumer(), null, array(
                'cache_codes' => implode('|', $cache_codes),
                'fields' => 'internal_id|code|name|location|type|status|rating|recommendations|founds|trackables_count'
            ));
            $internal_request->skip_limits = true;
            $caches = OkapiServiceRunner::call("services/caches/geocaches", $internal_request);

            foreach ($caches as $cache)
            {
                $row = self::generate_short_row($cache);
                if (!$row) {
                    /* Some caches cannot be included, e.g. the ones near the poles. */
                    continue;
                }
                Db::execute("
                    replace into okapi_tile_caches (
                        z, x, y, cache_id, z21x, z21y, status, type, rating, flags, name_crc
                    ) values (
                        0, 0, 0,
                        '".Db::escape_string($row[0])."',
                        '".Db::escape_string($row[1])."',
                        '".Db::escape_string($row[2])."',
                        '".Db::escape_string($row[3])."',
                        '".Db::escape_string($row[4])."',
                        ".(($row[5] === null) ? "null" : "'".Db::escape_string($row[5])."'").",
                        '".Db::escape_string($row[6])."',
                        '".Db::escape_string($row[7])."'
                    );
                ");
            }
            $status = 2;
        }
        else
        {
            # We will use the parent tile to compute the contents of this tile.

            $parent_zoom = $zoom - 1;
            $parent_x = $x >> 1;
            $parent_y = $y >> 1;

            $status = self::get_tile_status($parent_zoom, $parent_x, $parent_y);
            if ($status === null)  # Not computed.
            {
                $time_started = microtime(true);
                $status = self::compute_tile($parent_zoom, $parent_x, $parent_y);
            }

            if ($status === 1)  # Computed and empty.
            {
                # No need to check.
            }
            else  # Computed, not empty.
            {
                $scale = 8 + 21 - $zoom;
                $parentcenter_z21x = (($parent_x << 1) | 1) << $scale;
                $parentcenter_z21y = (($parent_y << 1) | 1) << $scale;
                $margin = 1 << ($scale - 2);
                $left_z21x = (($parent_x << 1) << $scale) - $margin;
                $right_z21x = ((($parent_x + 1) << 1) << $scale) + $margin;
                $top_z21y = (($parent_y << 1) << $scale) - $margin;
                $bottom_z21y = ((($parent_y + 1) << 1) << $scale) + $margin;

                # Choose the right quarter.
                # |1 2|
                # |3 4|

                if ($x & 1)  # 2 or 4
                    $left_z21x = $parentcenter_z21x - $margin;
                else  # 1 or 3
                    $right_z21x = $parentcenter_z21x + $margin;
                if ($y & 1)  # 3 or 4
                    $top_z21y = $parentcenter_z21y - $margin;
                else  # 1 or 2
                    $bottom_z21y = $parentcenter_z21y + $margin;

                # Cache the result.

                # Avoid deadlocks, see https://github.com/opencaching/okapi/issues/388
                # Possible alternative: https://github.com/opencaching/okapi/issues/388#issuecomment-197673621
                Db::execute("lock tables okapi_tile_caches write, okapi_tile_caches tc2 read");
                try {
                    Db::execute("
                        replace into okapi_tile_caches (
                            z, x, y, cache_id, z21x, z21y, status, type, rating,
                            flags, name_crc
                        )
                        select
                            '".Db::escape_string($zoom)."',
                            '".Db::escape_string($x)."',
                            '".Db::escape_string($y)."',
                            cache_id, z21x, z21y, status, type, rating,
                            flags, name_crc
                        from okapi_tile_caches tc2
                        where
                            z = '".Db::escape_string($parent_zoom)."'
                            and x = '".Db::escape_string($parent_x)."'
                            and y = '".Db::escape_string($parent_y)."'
                            and z21x between $left_z21x and $right_z21x
                            and z21y between $top_z21y and $bottom_z21y
                    ");
                } finally {
                    Db::execute("unlock tables");
                }
                $test = Db::select_value("
                    select 1
                    from okapi_tile_caches
                    where
                        z = '".Db::escape_string($zoom)."'
                        and x = '".Db::escape_string($x)."'
                        and y = '".Db::escape_string($y)."'
                    limit 1;
                ");
                if ($test)
                    $status = 2;
                else
                    $status = 1;
            }
        }

        # Mark tile as computed.

        Db::execute("
            replace into okapi_tile_status (z, x, y, status)
            values (
                '".Db::escape_string($zoom)."',
                '".Db::escape_string($x)."',
                '".Db::escape_string($y)."',
                '".Db::escape_string($status)."'
            );
        ");

        return $status;
    }

    /**
     * Convert OKAPI's cache object to a short database row to be inserted
     * into okapi_tile_caches table. Returns the list of the following attributes:
     * cache_id, z21x, z21y, status, type, rating, flags, name_crc
     * (rating may be null!).
     */
    public static function generate_short_row($cache)
    {
        list($lat, $lon) = explode("|", $cache['location']);
        try {
            list($z21x, $z21y) = self::latlon_to_z21xy($lat, $lon);
        } catch (Exception $e) {
            /* E.g. division by zero, if the cache is placed at the north pole. */
            return false;
        }
        $flags = 0;
        if (($cache['founds'] > 6) && (($cache['recommendations'] / $cache['founds']) > 0.3))
            $flags |= self::$FLAG_STAR;
        if ($cache['trackables_count'] > 0)
            $flags |= self::$FLAG_HAS_TRACKABLES;
        if ($cache['founds'] == 0)
            $flags |= self::$FLAG_NOT_YET_FOUND;
        return array($cache['internal_id'], $z21x, $z21y, Okapi::cache_status_name2id($cache['status']),
            Okapi::cache_type_name2id($cache['type']), $cache['rating'], $flags,
            self::compute_name_crc($cache['name']));
    }

    private static function compute_name_crc($name)
    {
        /* Unfortunatelly, crc32 behaves differently on different platforms
         * (returned signed integers on 32bit, unsigned on 64bit). In order to
         * avoid MySQL casting negative values to 0, we'll do an additional
         * abs on it. */

         return abs(crc32($name));
    }

    private static function latlon_to_z21xy($lat, $lon)
    {
        $offset = 128 << 21;
        $x = round($offset + ($offset * $lon / 180));
        $y = round($offset - $offset/pi() * log((1 + sin($lat * pi() / 180)) / (1 - sin($lat * pi() / 180))) / 2);
        return array($x, $y);
    }
}
