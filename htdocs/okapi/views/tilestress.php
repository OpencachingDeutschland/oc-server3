<?php

namespace okapi\views\tilestress;

use Exception;
use okapi\Db;
use okapi\OkapiExceptionHandler;
use okapi\OkapiInternalAccessToken;
use okapi\OkapiInternalConsumer;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;

class View
{
    public static function out($str)
    {
        print $str;
        flush();
    }

    public static function call()
    {
        # By default, this view is turned off in the OkapiUrls.php file.
        # This view is for debugging TileMap performace only!

        set_time_limit(0);

        header("Content-Type: text/plain; charset=utf-8");

        $user_id = $_GET['u'];
        self::out("Yo. I'm $user_id.\n\n");

        while (true)
        {
            srand(floor(time() / 10));
            $mode2 = rand(0, 9) <= 7;
            if ($mode2) {
                $row = Db::select_row("
                    select z, x, y
                    from okapi_tile_status
                    where status = 2 and z < 20
                    order by rand()
                    limit 1;
                ");
                $z = $row['z'] + 1;
                $x = $row['x'] << 1;
                $y = $row['y'] << 1;
                $x += rand(0, 1);
                $y += rand(0, 1);
            } else {
                $z = rand(5, 21);
                $x = rand(0, (1 << $z) - 1);
                $y = rand(0, (1 << $z) - 1);
            }

            $tiles = array();
            for ($xx=$x; $xx<$x+4; $xx++)
                for ($yy=$y; $yy<$y+4; $yy++)
                    $tiles[] = array($xx, $yy);
            srand();
            shuffle($tiles);

            foreach ($tiles as $tile)
            {
                list($x, $y) = $tile;
                self::out("Loading ".str_pad("($z, $x, $y)... ", 30));
                $time_started = microtime(true);
                try {
                    $response = OkapiServiceRunner::call('services/caches/map/tile', new OkapiInternalRequest(
                        new OkapiInternalConsumer(), new OkapiInternalAccessToken($user_id),
                        array('z' => "$z", 'x' => "$x", 'y' => "$y")));
                    $runtime = microtime(true) - $time_started;
                    $ds = floor($runtime * 100);
                    self::out(str_repeat("#", $ds)." ");
                    $b = floor(strlen($response->get_body()) / 256);
                    self::out(str_repeat("@", $b)."\n");
                } catch (Exception $e) {
                    self::out("\n\n".OkapiExceptionHandler::get_exception_info($e));
                    die();
                }
            }
        }
    }

}
