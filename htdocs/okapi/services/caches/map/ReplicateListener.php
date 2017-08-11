<?php

namespace okapi\services\caches\map;

use okapi\Db;
use okapi\InvalidParam;
use okapi\OkapiInternalConsumer;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;

class ReplicateListener
{
    public static function receive($changelog)
    {
        # This will be called every time new items arrive from replicate module's
        # changelog. The format of $changelog is described in the replicate module
        # (NOT the entire response, just the "changelog" key).

        foreach ($changelog as $c)
        {
            if ($c['object_type'] == 'geocache')
            {
                if ($c['change_type'] == 'replace')
                    self::handle_geocache_replace($c);
                else
                    self::handle_geocache_delete($c);
            }
        }
    }

    public static function reset()
    {
        # This will be called when there are "too many" entries in the changelog
        # and the replicate module thinks it better to just reset the entire TileTree.
        # For the first hours after such reset maps may work a little slower.

        Db::execute("delete from okapi_tile_status");
        Db::execute("delete from okapi_tile_caches");
    }

    private static function handle_geocache_replace($c)
    {
        # Check if any relevant geocache attributes have changed.
        # We will pick up "our" copy of the cache from zero-zoom level.

        try {
            $cache = OkapiServiceRunner::call("services/caches/geocache", new OkapiInternalRequest(new OkapiInternalConsumer(), null, array(
                'cache_code' => $c['object_key']['code'],
                'fields' => 'internal_id|code|name|location|type|status|rating|recommendations|founds|trackables_count'
            )));
        } catch (InvalidParam $e) {
            # Unprobable, but possible. Ignore changelog entry.
            return;
        }

        # Fetch our copy of the cache.

        $ours = Db::fetch_row(Db::query("
            select cache_id, z21x, z21y, status, type, rating, flags, name_crc
            from okapi_tile_caches
            where
                z=0
                and cache_id = '".Db::escape_string($cache['internal_id'])."'
        "));

        # Caches near the poles caused our computations to break here. We will
        # ignore such caches!

        list($lat, $lon) = explode("|", $cache['location']);
        if ((floatval($lat) >= 89.99) || (floatval($lat) <= -89.99)) {
            if ($ours) {
                self::remove_geocache_from_cached_tiles($ours[0]);
            }
            return;
        }

        # Compute the new row for okapi_tile_caches. Compare with the old one.

        $theirs = TileTree::generate_short_row($cache);
        if (!$ours)
        {
            # Aaah, a new geocache! How nice... ;)

            self::add_geocache_to_cached_tiles($theirs);
        }
        elseif (($ours[1] != $theirs[1]) || ($ours[2] != $theirs[2]))  # z21x & z21y fields
        {
            # Location changed.

            self::remove_geocache_from_cached_tiles($ours[0]);
            self::add_geocache_to_cached_tiles($theirs);
        }
        elseif ($ours != $theirs)
        {
            self::update_geocache_attributes_in_cached_tiles($theirs);
        }
        else
        {
            # No need to update anything. This is very common (i.e. when the
            # cache was simply found, not actually changed). Replicate module generates
            # many updates which do not influence our cache.
        }
    }

    private static function remove_geocache_from_cached_tiles($cache_id)
    {
        # Simply remove all traces of this geocache from all tiles.
        # This includes all references along tiles' borders, etc.

        Db::execute("
            delete from okapi_tile_caches
            where cache_id = '".Db::escape_string($cache_id)."'
        ");

        # Note, that after this operation, okapi_tile_status may be out-of-date.
        # There might exist some rows with status==2, but they should be in status==1.
        # Currently, we can ignore this, because status==1 is just a shortcut to
        # avoid making unnecessary queries.
    }

    private static function add_geocache_to_cached_tiles(&$row)
    {
        # This one is the most complicated. We need to identify all tiles
        # where the cache should be present. This include 22 obvious "exact match"
        # tiles (one per each zoom level), *and* all "just outside the border"
        # tiles (one geocache can be present in up to 4 tiles per zoom level).
        # This gives us max. 88 tiles to add the geocache to.

        $tiles_to_update = array();

        # We will begin at zoom 21 and then go down to zoom 0.

        $z21x = $row[1];
        $z21y = $row[2];
        $ex = $z21x >> 8;  # initially, z21x / <tile width>
        $ey = $z21y >> 8;  # initially, z21y / <tile height>
        for ($zoom = 21; $zoom >= 0; $zoom--, $ex >>= 1, $ey >>= 1)
        {
            # ($ex, $ey) points to the "exact match" tile. We need to determine
            # tile-range to check for "just outside the border" tiles. We will
            # go with the simple approach and check all 1+8 bordering tiles.

            $tiles_in_this_region = array();
            for ($x=$ex-1; $x<=$ex+1; $x++)
                for ($y=$ey-1; $y<=$ey+1; $y++)
                    if (($x >= 0) && ($x < 1<<$zoom) && ($y >= 0) && ($y < 1<<$zoom))
                        $tiles_in_this_region[] = array($x, $y);

            foreach ($tiles_in_this_region as $coords)
            {
                list($x, $y) = $coords;

                $scale = 8 + 21 - $zoom;
                $margin = 1 << ($scale - 3);  # 32px of current $zoom level, measured in z21 pixels.

                $left_z21x = ($x << $scale) - $margin;
                $right_z21x = (($x + 1) << $scale) + $margin;
                $top_z21y = ($y << $scale) - $margin;
                $bottom_z21y = (($y + 1) << $scale) + $margin;

                if ($z21x < $left_z21x)
                    continue;
                if ($z21x > $right_z21x)
                    continue;
                if ($z21y < $top_z21y)
                    continue;
                if ($z21y > $bottom_z21y)
                    continue;

                # We found a match. Store it for later.

                $tiles_to_update[] = array($zoom, $x, $y);
            }
        }

        # We have a list of all possible tiles that need updating.
        # Most of these tiles aren't cached at all. We need to update
        # only the cached ones.

        $alternatives_escaped = array();
        foreach ($tiles_to_update as $coords)
        {
            list($z, $x, $y) = $coords;
            $alternatives_escaped[] = "(
                z = '".Db::escape_string($z)."'
                and x = '".Db::escape_string($x)."'
                and y = '".Db::escape_string($y)."'
            )";
        }
        if (count($alternatives_escaped) > 0)
        {
            Db::execute("
                replace into okapi_tile_caches (
                    z, x, y, cache_id, z21x, z21y, status, type, rating, flags, name_crc
                )
                select
                    z, x, y,
                    '".Db::escape_string($row[0])."',
                    '".Db::escape_string($row[1])."',
                    '".Db::escape_string($row[2])."',
                    '".Db::escape_string($row[3])."',
                    '".Db::escape_string($row[4])."',
                    ".(($row[5] === null) ? "null" : "'".Db::escape_string($row[5])."'").",
                    '".Db::escape_string($row[6])."',
                    '".Db::escape_string($row[7])."'
                from okapi_tile_status
                where
                    (".implode(" or ", $alternatives_escaped).")
                    and status in (1,2)
            ");

            # We might have just filled some empty tiles (status 1) with data.
            # We need to update their status to 2.

            Db::execute("
                update okapi_tile_status
                set status=2
                where
                    (".implode(" or ", $alternatives_escaped).")
                    and status=1
            ");
        }

        # And that's all. That should do the trick.
    }

    private static function update_geocache_attributes_in_cached_tiles(&$row)
    {
        # Update all attributes (for all levels). Note, that we don't need to
        # update location ($row[1] and $row[2]) - this method is called ONLY
        # when location stayed untouched!

        Db::execute("
            update okapi_tile_caches
            set
                status = '".Db::escape_string($row[3])."',
                type = '".Db::escape_string($row[4])."',
                rating = ".(($row[5] === null) ? "null" : "'".Db::escape_string($row[5])."'").",
                flags = '".Db::escape_string($row[6])."',
                name_crc = '".Db::escape_string($row[7])."'
            where
                cache_id = '".Db::escape_string($row[0])."'
        ");
    }

    private static function handle_geocache_delete($c)
    {
        # Simply delete the cache at all zoom levels.

        $cache_id = Db::select_value("
            select cache_id
            from caches
            where wp_oc='".Db::escape_string($c['object_key']['code'])."'
        ");
        self::remove_geocache_from_cached_tiles($cache_id);
    }
}
