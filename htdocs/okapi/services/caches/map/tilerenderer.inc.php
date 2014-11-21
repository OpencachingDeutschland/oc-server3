<?php

namespace okapi\services\caches\map;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\Cache;
use okapi\Db;
use okapi\FileCache; // WRTODO

interface TileRenderer
{
    /**
     * Return the unique hash of the tile being rendered. This method will be
     * called only once, prior the render method. You may (but don't have to)
     * throw an Exception on every subsequent call.
     */
    public function get_unique_hash();

    /** Get the content type of the data returned by the render method. */
    public function get_content_type();

    /**
     * Render the image. This function will be called only once, after calling
     * get_unique_hash method.
     */
    public function render();
}

class DefaultTileRenderer implements TileRenderer
{
    /**
     * Changing this will affect all generated hashes. You should increment it
     * whenever you alter anything in the drawing algorithm.
     */
    private static $VERSION = 59;

    /**
     * Should be always true. You may temporarily set it to false, when you're
     * testing/debugging a new set of static icons.
     */
    private static $USE_STATIC_IMAGE_CACHE = true;

    /**
     * Should be always true. You may temporarily set it to false, when you're
     * testing/debugging a new captions renderer.
     */
    private static $USE_CAPTIONS_CACHE = true;

    private $zoom;
    private $rows_ref;
    private $im;

    /**
     * Takes the zoom level and the list of geocache descriptions. Note, that
     * $rows_ref can be altered after calling render. If you don't want it to,
     * you should pass a deep copy.
     */
    public function __construct($zoom, &$rows_ref)
    {
        $this->zoom = $zoom;
        $this->rows_ref = &$rows_ref;
    }

    public function get_unique_hash()
    {
        return md5(json_encode(array(
            "DefaultTileRenderer",
            self::$VERSION,
            $this->zoom,
            $this->rows_ref
        )));
    }

    public function get_content_type()
    {
        return "image/png";
    }

    public function render()
    {
        # Preprocess the rows.

        if ($this->zoom >= 5)
            $this->decide_which_get_captions();

        # Make a background.

        $this->im = imagecreatetruecolor(256, 256);
        imagealphablending($this->im, false);
        if ($this->zoom >= 13) $opacity = 15;
        elseif ($this->zoom <= 12) $opacity = max(0, $this->zoom * 2 - 14);
        $transparent = imagecolorallocatealpha($this->im, 0, 0, 0, 127 - $opacity);
        imagefilledrectangle($this->im, 0, 0, 256, 256, $transparent);
        imagealphablending($this->im, true);

        # Draw the caches.

        foreach ($this->rows_ref as &$row_ref)
            $this->draw_cache($row_ref);

        # Return the result.

        ob_start();
        imagesavealpha($this->im, true);
        imagepng($this->im);
        imagedestroy($this->im);
        return ob_get_clean();
    }

    private static function get_image($name, $opacity=1, $brightness=0,
        $contrast=0, $r=0, $g=0, $b=0)
    {
        static $locmem_cache = array();

        # Check locmem cache.

        $key = "$name/$opacity/$brightness/$contrast/$r/$g/$b";
        if (!isset($locmem_cache[$key]))
        {
            # Miss. Check default cache.  WRTODO: upgrade to normal Cache?

            try
            {
                $cache_key = "tilesrc/".Okapi::$revision."/".self::$VERSION."/".$key;
                $gd2_path = self::$USE_STATIC_IMAGE_CACHE
                    ? FileCache::get_file_path($cache_key) : null;
                if ($gd2_path === null)
                    throw new Exception("Not in cache");
                # File cache hit. GD2 files are much faster to read than PNGs.
                # This can throw an Exception (see bug#160).
                $locmem_cache[$key] = imagecreatefromgd2($gd2_path);
            }
            catch (Exception $e)
            {
                # Miss again (or error decoding). Read the image from PNG.

                $locmem_cache[$key] = imagecreatefrompng($GLOBALS['rootpath']."okapi/static/tilemap/$name.png");

                # Apply all wanted effects.

                if ($opacity != 1)
                    self::change_opacity($locmem_cache[$key], $opacity);
                if ($contrast != 0)
                    imagefilter($locmem_cache[$key], IMG_FILTER_CONTRAST, $contrast);
                if ($brightness != 0)
                    imagefilter($locmem_cache[$key], IMG_FILTER_BRIGHTNESS, $brightness);
                if (($r != 0) || ($g != 0) || ($b != 0))
                {
                    imagefilter($locmem_cache[$key], IMG_FILTER_GRAYSCALE);
                    imagefilter($locmem_cache[$key], IMG_FILTER_COLORIZE, $r, $g, $b);
                }

                # Cache the result.

                ob_start();
                imagegd2($locmem_cache[$key]);
                $gd2 = ob_get_clean();
                FileCache::set($cache_key, $gd2);
            }
        }
        return $locmem_cache[$key];
    }

    /**
     * Extremely slow! Remember to cache the result!
     */
    private static function change_opacity($im, $ratio)
    {
        imagealphablending($im, false);

        $w = imagesx($im);
        $h = imagesy($im);

        for($x = 0; $x < $w; $x++)
        {
            for($y = 0; $y < $h; $y++)
            {
                $color = imagecolorat($im, $x, $y);
                $new_color = ((max(0, floor(127 - ((127 - (($color >> 24) & 0x7f)) * $ratio))) & 0x7f) << 24) | ($color & 0x80ffffff);
                imagesetpixel($im, $x, $y, $new_color);
            }
        }

        imagealphablending($im, true);
    }

    private function draw_cache(&$cache_struct)
    {
        $capt = ($cache_struct[6] & TileTree::$FLAG_DRAW_CAPTION);
        if (($this->zoom <= 8) && (!$capt))
            $this->draw_cache_tiny($cache_struct);
        elseif (($this->zoom <= 13) && (!$capt))
            $this->draw_cache_medium($cache_struct);
        else
            $this->draw_cache_large($cache_struct);

        # Put caption (this flag is set only when there is plenty of space around).

        if ($cache_struct[6] & TileTree::$FLAG_DRAW_CAPTION)
        {
            $caption = $this->get_caption($cache_struct[0]);
            imagecopy($this->im, $caption, $cache_struct[1] - 32, $cache_struct[2] + 6, 0, 0, 64, 26);
        }

    }


    private function draw_cache_large(&$cache_struct)
    {
        list($cache_id, $px, $py, $status, $type, $rating, $flags, $count) = $cache_struct;

        $found = $flags & TileTree::$FLAG_FOUND;
        $own = $flags & TileTree::$FLAG_OWN;
        $new = $flags & TileTree::$FLAG_NEW;

        # Prepare vars.

        if ($own) {
            $key = 'large_outer_own';
            $a = 1; $br = 0; $c = 0;
            $r = 0; $g = 0; $b = 0;
        } elseif ($found) {
            $key = 'large_outer_found';
            $a = ($flags & TileTree::$FLAG_DRAW_CAPTION) ? .7 : .35;
            $br = 40; $c = 20;
            //$a = 0.5; $br = 0; $c = 0;
            $r = 0; $g = 0; $b = 0;
        } elseif ($new) {
            $key = 'large_outer_new';
            $a = 1; $br = 0; $c = 0;
            $r = 0; $g = 0; $b = 0;
        } else {
            $key = 'large_outer';
            $a = 1; $br = 0; $c = 0;
            $r = 0; $g = 0; $b = 0;
        }

        # Put the outer marker (indicates the found/new/own status).

        $outer_marker = self::get_image($key, $a);

        $width = 40;
        $height = 32;
        $center_x = 12;
        $center_y = 26;
        $markercenter_x = 12;
        $markercenter_y = 12;

        if ($count > 1)
            imagecopy($this->im, $outer_marker, $px - $center_x + 3, $py - $center_y - 2, 0, 0, $width, $height);
        imagecopy($this->im, $outer_marker, $px - $center_x, $py - $center_y, 0, 0, $width, $height);

        # Put the inner marker (indicates the type).

        $inner_marker = self::get_image("large_inner_".self::get_type_suffix(
            $type, true), $a, $br, $c, $r, $g, $b);
        imagecopy($this->im, $inner_marker, $px - 7, $py - 22, 0, 0, 16, 16);

        # If the cache is unavailable, mark it with X.

        if (($status != 1) && ($count == 1))
        {
            $icon = self::get_image(($status == 2) ? "status_unavailable"
                : "status_archived", $a);
            imagecopy($this->im, $icon, $px - 1, $py - $center_y - 4, 0, 0, 16, 16);
        }

        # Put the rating smile. :)

        if ($status == 1)
        {
            if ($rating >= 4.2)
            {
                if ($flags & TileTree::$FLAG_STAR) {
                    $icon = self::get_image("rating_grin", $a, $br, $c, $r, $g, $b);
                    imagecopy($this->im, $icon, $px - 7 - 6, $py - $center_y - 8, 0, 0, 16, 16);
                    $icon = self::get_image("rating_star", $a, $br, $c, $r, $g, $b);
                    imagecopy($this->im, $icon, $px - 7 + 6, $py - $center_y - 8, 0, 0, 16, 16);
                } else {
                    $icon = self::get_image("rating_grin", $a, $br, $c, $r, $g, $b);
                    imagecopy($this->im, $icon, $px - 7, $py - $center_y - 8, 0, 0, 16, 16);
                }
            }
            # This was commented out because users complained about too many smiles ;)
            // elseif ($rating >= 3.6) {
            //   $icon = self::get_image("rating_smile", $a, $br, $c, $r, $g, $b);
            //   imagecopy($this->im, $icon, $px - 7, $py - $center_y - 8, 0, 0, 16, 16);
            // }
        }

        # Mark found caches with V.

        if ($found)
        {
            $icon = self::get_image("found", 0.7*$a, $br, $c, $r, $g, $b);
            imagecopy($this->im, $icon, $px - 2, $py - $center_y - 3, 0, 0, 16, 16);
        }

    }

    /**
     * Split lines so that they fit inside the specified width.
     */
    private static function wordwrap($font, $size, $maxWidth, $text)
    {
        $words = explode(" ", $text);
        $lines = array();
        $line = "";
        $nextBonus = "";
        for ($i=0; ($i<count($words)) || (mb_strlen($nextBonus)>0); $i++) {
            $word = isset($words[$i])?$words[$i]:"";
            if (mb_strlen($nextBonus) > 0)
                $word = $nextBonus." ".$word;
            $nextBonus = "";
            while (true) {
                $bbox = imagettfbbox($size, 0, $font, $line.$word);
                $width = $bbox[2]-$bbox[0];
                if ($width <= $maxWidth) {
                    $line .= $word." ";
                    continue 2;
                }
                if (mb_strlen($line) > 0) {
                    $lines[] = trim($line);
                    $line = "";
                    continue;
                }
                $nextBonus = $word[mb_strlen($word)-1].$nextBonus;
                $word = mb_substr($word, 0, mb_strlen($word)-1);
                continue;
            }
        }
        if (mb_strlen($line) > 0)
            $lines[] = trim($line);
        return implode("\n", $lines);
    }

    /**
     * Return 64x26 bitmap with the caption (name) for the given geocache.
     */
    private function get_caption($cache_id)
    {
        # Check cache.

        $cache_key = "tilecaption/".self::$VERSION."/".$cache_id;
        $gd2 = self::$USE_CAPTIONS_CACHE ? Cache::get($cache_key) : null;
        if ($gd2 === null)
        {
            # We'll work with 16x bigger image to get smoother interpolation.

            $im = imagecreatetruecolor(64*4, 26*4);
            imagealphablending($im, false);
            $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            imagefilledrectangle($im, 0, 0, 64*4, 26*4, $transparent);
            imagealphablending($im, true);

            # Get the name of the cache.

            $name = Db::select_value("
                select name
                from caches
                where cache_id = '".mysql_real_escape_string($cache_id)."'
            ");

            # Split the name into a couple of lines.

            //$font = $GLOBALS['rootpath'].'util.sec/bt.ttf';
            $font = $GLOBALS['rootpath'].'okapi/static/tilemap/tahoma.ttf';
            $size = 25;
            $lines = explode("\n", self::wordwrap($font, $size, 64*4 - 6*2, $name));

            # For each line, compute its (x, y) so that the text is centered.

            $y = 0;
            $positions = array();
            foreach ($lines as $line)
            {
                $bbox = imagettfbbox($size, 0, $font, $line);
                $width = $bbox[2]-$bbox[0];
                $x = 128 - ($width >> 1);
                $positions[] = array($x, $y);
                $y += 36;
            }
            $drawer = function($x, $y, $color) use (&$lines, &$positions, &$im, &$size, &$font)
            {
                $len = count($lines);
                for ($i=0; $i<$len; $i++)
                {
                    $line = $lines[$i];
                    list($offset_x, $offset_y) = $positions[$i];
                    imagettftext($im, $size, 0, $offset_x + $x, $offset_y + $y, $color, $font, $line);
                }
            };

            # Draw an outline.

            $outline_color = imagecolorallocatealpha($im, 255, 255, 255, 80);
            for ($x=0; $x<=12; $x+=3)
                for ($y=$size-3; $y<=$size+9; $y+=3)
                    $drawer($x, $y, $outline_color);

            # Add a slight shadow effect (on top of the outline).

            $drawer(9, $size + 3, imagecolorallocatealpha($im, 0, 0, 0, 110));

            # Draw the caption.

            $drawer(6, $size + 3, imagecolorallocatealpha($im, 150, 0, 0, 40));

            # Resample.

            imagealphablending($im, false);
            $small = imagecreatetruecolor(64, 26);
            imagealphablending($small, false);
            imagecopyresampled($small, $im, 0, 0, 0, 0, 64, 26, 64*4, 26*4);

            # Cache it!

            ob_start();
            imagegd2($small);
            $gd2 = ob_get_clean();
            Cache::set_scored($cache_key, $gd2);
        }

        return imagecreatefromstring($gd2);
    }

    private function draw_cache_medium(&$cache_struct)
    {
        list($cache_id, $px, $py, $status, $type, $rating, $flags, $count) = $cache_struct;

        $found = $flags & TileTree::$FLAG_FOUND;
        $own = $flags & TileTree::$FLAG_OWN;
        $new = $flags & TileTree::$FLAG_NEW;
        if ($found && (!($flags & TileTree::$FLAG_DRAW_CAPTION)))
            $a = .35;
        else
            $a = 1;

        # Put the marker (indicates the type).

        $marker = self::get_image("medium_".self::get_type_suffix($type, false), $a);
        $width = 14;
        $height = 14;
        $center_x = 7;
        $center_y = 8;
        $markercenter_x = 7;
        $markercenter_y = 8;

        if ($count > 1)
        {
            imagecopy($this->im, $marker, $px - $center_x + 3, $py - $center_y - 2, 0, 0, $width, $height);
            imagecopy($this->im, $marker, $px - $center_x, $py - $center_y, 0, 0, $width, $height);
        }
        elseif ($status == 1)  # don't put the marker for unavailable caches (X only)
        {
            imagecopy($this->im, $marker, $px - $center_x, $py - $center_y, 0, 0, $width, $height);
        }

        # If the cache is unavailable, mark it with X.

        if (($status != 1) && ($count == 1))
        {
            $icon = self::get_image(($status == 2) ? "status_unavailable"
                : "status_archived");
            imagecopy($this->im, $icon, $px - ($center_x - $markercenter_x) - 6,
                $py - ($center_y - $markercenter_y) - 8, 0, 0, 16, 16);
        }

        # Put small versions of rating icons.

        if ($status == 1)
        {
            if ($rating >= 4.2)
            {
                if ($flags & TileTree::$FLAG_STAR) {
                    $icon = self::get_image("rating_grin_small", max(0.6, $a));
                    imagecopy($this->im, $icon, $px - 5, $py - $center_y - 1, 0, 0, 6, 6);
                    $icon = self::get_image("rating_star_small", max(0.6, $a));
                    imagecopy($this->im, $icon, $px - 2, $py - $center_y - 3, 0, 0, 10, 10);
                } else {
                    $icon = self::get_image("rating_grin_small", max(0.6, $a));
                    imagecopy($this->im, $icon, $px - 3, $py - $center_y - 1, 0, 0, 6, 6);
                }
            }
        }

        if ($own)
        {
            # Mark own caches with additional overlay.

            $overlay = self::get_image("medium_overlay_own");
            imagecopy($this->im, $overlay, $px - $center_x, $py - $center_y, 0, 0, $width, $height);
        }
        elseif ($found)
        {
            # Mark found caches with V.

            $icon = self::get_image("found", 0.7*$a);
            imagecopy($this->im, $icon, $px - ($center_x - $markercenter_x) - 7,
                $py - ($center_y - $markercenter_y) - 9, 0, 0, 16, 16);
        }
        elseif ($new)
        {
            # Mark new caches with additional overlay.

            $overlay = self::get_image("medium_overlay_new");
            imagecopy($this->im, $overlay, $px - $center_x, $py - $center_y, 0, 0, $width, $height);
        }
    }

    private static function get_type_suffix($type, $extended_set)
    {
        switch ($type) {
            case 2: return 'traditional';
            case 3: return 'multi';
            case 6: return 'event';
            case 7: return 'quiz';
            case 4: return 'virtual';
            case 1: return 'unknown';
        }
        if ($extended_set)
        {
            switch ($type) {
                case 10: return 'own';
                case 8: return 'moving';
                case 5: return 'webcam';
            }
        }
        return 'other';
    }

    private function draw_cache_tiny(&$cache_struct)
    {
        list($cache_id, $px, $py, $status, $type, $rating, $flags, $count) = $cache_struct;

        $found = $flags & TileTree::$FLAG_FOUND;
        $own = $flags & TileTree::$FLAG_OWN;
        $new = $flags & TileTree::$FLAG_NEW;

        $marker = self::get_image("tiny_".self::get_type_suffix($type, false));
        $width = 10;
        $height = 10;
        $center_x = 5;
        $center_y = 6;
        $markercenter_x = 5;
        $markercenter_y = 6;

        # Put the marker. If cache covers more caches, then put two markers instead of one.

        if ($count > 1)
        {
            imagecopy($this->im, $marker, $px - $center_x + 3, $py - $center_y - 2, 0, 0, $width, $height);
            imagecopy($this->im, $marker, $px - $center_x, $py - $center_y, 0, 0, $width, $height);
        }
        elseif ($status == 1)
        {
            imagecopy($this->im, $marker, $px - $center_x, $py - $center_y, 0, 0, $width, $height);
        }

        # If the cache is unavailable, mark it with X.

        if (($status != 1) && ($count == 1))
        {
            $icon = self::get_image(($status == 2) ? "status_unavailable"
                : "status_archived");
            imagecopy($this->im, $icon, $px - ($center_x - $markercenter_x) - 6,
                $py - ($center_y - $markercenter_y) - 8, 0, 0, 16, 16);
        }
    }

    /**
     * Examine all rows and decide which of them will get captions.
     * Mark selected rows with TileTree::$FLAG_DRAW_CAPTION.
     *
     * Note: Calling this will alter the rows!
     */
    private function decide_which_get_captions()
    {
        # We will split the tile (along with its margins) into 12x12 squares.
        # A single geocache placed in square (x, y) gets the caption only
        # when there are no other geocaches in any of the adjacent squares.
        # This is efficient and yields acceptable results.

        $matrix = array();
        for ($i=0; $i<12; $i++)
            $matrix[] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        foreach ($this->rows_ref as &$row_ref)
        {
            $mx = ($row_ref[1] + 64) >> 5;
            $my = ($row_ref[2] + 64) >> 5;
            if (($mx >= 12) || ($my >= 12)) continue;
            if (($matrix[$mx][$my] === 0) && ($row_ref[7] == 1))  # 7 is count
                $matrix[$mx][$my] = $row_ref[0];  # 0 is cache_id
            else
                $matrix[$mx][$my] = -1;
        }
        $selected_cache_ids = array();
        for ($mx=1; $mx<11; $mx++)
        {
            for ($my=1; $my<11; $my++)
            {
                if ($matrix[$mx][$my] > 0)  # cache_id
                {
                    # Check all adjacent squares.

                    if (   ($matrix[$mx-1][$my-1] === 0)
                        && ($matrix[$mx-1][$my  ] === 0)
                        && ($matrix[$mx-1][$my+1] === 0)
                        && ($matrix[$mx  ][$my-1] === 0)
                        && ($matrix[$mx  ][$my+1] === 0)
                        && ($matrix[$mx+1][$my-1] === 0)
                        && ($matrix[$mx+1][$my  ] === 0)
                        && ($matrix[$mx+1][$my+1] === 0)
                    )
                        $selected_cache_ids[] = $matrix[$mx][$my];
                }
            }
        }

        foreach ($this->rows_ref as &$row_ref)
            if (in_array($row_ref[0], $selected_cache_ids))
                $row_ref[6] |= TileTree::$FLAG_DRAW_CAPTION;
    }

}