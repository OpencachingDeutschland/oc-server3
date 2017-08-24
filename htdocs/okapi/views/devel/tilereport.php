<?php

namespace okapi\views\devel\tilereport;

use okapi\cronjobs\CronJobController;
use okapi\Db;
use okapi\Okapi;
use okapi\OkapiHttpResponse;

class View
{
    public static function call()
    {
        Okapi::require_developer_cookie();

        require_once __DIR__ . '/../../cronjobs.php';
        CronJobController::force_run("StatsWriterCronJob");

        # When services/caches/map/tile method is called, it writes some extra
        # stats in the okapi_stats_hourly table. This page retrieves and
        # formats these stats in a readable manner (for debugging).

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        ob_start();

        $start = isset($_GET['start']) ? $_GET['start'] : date(
            "Y-m-d 00:00:00", time() - 7*86400);
        $end = isset($_GET['end']) ? $_GET['end'] : date("Y-m-d 23:59:59");

        print "From: $start\n";
        print "  To: $end\n\n";

        $rs = Db::query("
            select
                service_name,
                sum(total_calls),
                sum(total_runtime)
            from okapi_stats_hourly
            where
                period_start >= '".Db::escape_string($start)."'
                and period_start < '".Db::escape_string($end)."'
                and service_name like '%caches/map/tile%'
            group by service_name
        ");

        $total_calls = 0;
        $total_runtime = 0.0;
        $calls = array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0);
        $runtime = array('A' => 0.0, 'B' => 0.0, 'C' => 0.0, 'D' => 0.0);

        while (list($name, $c, $r) = Db::fetch_row($rs))
        {
            if ($name == 'services/caches/map/tile')
            {
                $total_calls = $c;
                $total_runtime = $r;
            }
            elseif (strpos($name, 'extra/caches/map/tile/checkpoint') === 0)
            {
                $calls[$name[32]] = $c;
                $runtime[$name[32]] = $r;
            }
        }
        if ($total_calls != $calls['A'])
        {
            print "Partial results. Only ".$calls['A']." out of $total_calls are covered.\n";
            print "All other will count as \"unaccounted for\".\n\n";
            $total_calls = $calls['A'];
        }

        $calls_left = $total_calls;
        $runtime_left = $total_runtime;

        $perc = function($a, $b) { return ($b > 0) ? sprintf("%.1f", 100 * $a / $b)."%" : "(?)"; };
        $avg = function($a, $b) { return ($b > 0) ? sprintf("%.4f", $a / $b)."s" : "(?)"; };
        $get_stats = function() use (&$calls_left, &$runtime_left, &$total_calls, &$total_runtime, &$perc)
        {
            return (
                str_pad($perc($calls_left, $total_calls), 6, " ", STR_PAD_LEFT).
                str_pad($perc($runtime_left, $total_runtime), 7, " ", STR_PAD_LEFT)
            );
        };

        print "%CALLS  %TIME  Description\n";
        print "====== ======  ======================================================================\n";
        print $get_stats()."  $total_calls responses served. Total runtime: ".sprintf("%.2f", $total_runtime)."s\n";
        print "\n";
        print "               All of these requests needed a TileTree build/lookup. The average runtime of\n";
        print "               these lookups was ".$avg($runtime['A'], $total_calls).". ".$perc($runtime['A'], $total_runtime)." of total runtime was spent here.\n";
        print "\n";

        $runtime_left -= $runtime['A'];

        print $get_stats()."  All calls passed here after ~".$avg($runtime['A'], $total_calls)."\n";

        print "\n";
        print "               Lookup result was then processed and \"image description\" was created. It was\n";
        print "               passed on to the TileRenderer to compute the ETag hash string. The average runtime\n";
        print "               of this part was ".$avg($runtime['B'], $total_calls).". ".$perc($runtime['B'], $total_runtime)." of total runtime was spent here.\n";
        print "\n";

        $runtime_left -= $runtime['B'];

        print $get_stats()."  All calls passed here after ~".$avg($runtime['A'] + $runtime['B'], $total_calls)."\n";

        $etag_hits = $calls['B'] - $calls['C'];

        print "\n";
        print "               $etag_hits of the requests matched the ETag and were served an HTTP 304 response.\n";
        print "\n";

        $calls_left = $calls['C'];

        print $get_stats()."  $calls_left calls passed here after ~".$avg($runtime['A'] + $runtime['B'], $total_calls)."\n";

        $imagecache_hits = $calls['C'] - $calls['D'];

        print "\n";
        print "               $imagecache_hits of these calls hit the server image cache.\n";
        print "               ".$perc($runtime['C'], $total_runtime)." of total runtime was spent to find these.\n";
        print "\n";

        $calls_left = $calls['D'];
        $runtime_left -= $runtime['C'];

        print $get_stats()."  $calls_left calls passed here after ~".$avg($runtime['A'] + $runtime['B'] + $runtime['C'], $total_calls)."\n";
        print "\n";
        print "               These calls required the tile to be rendered. On average, it took\n";
        print "               ".$avg($runtime['D'], $calls['D'])." to *render* a tile.\n";
        print "               ".$perc($runtime['D'], $total_runtime)." of total runtime was spent here.\n";
        print "\n";

        $runtime_left -= $runtime['D'];

        print $perc($runtime_left, $total_runtime)." of runtime was unaccounted for (other processing).\n";
        print "Average response time was ".$avg($total_runtime, $total_calls).".\n\n";

        print "Current okapi_cache score distribution:\n";
        $rs = Db::query("
            select floor(log2(score)), count(*), sum(length(value))
            from okapi_cache
            where score is not null
            group by floor(log2(score))
        ");
        while (list($log2, $count, $size) = Db::fetch_row($rs))
        {
            print $count." elements ($size bytes) with score between ".pow(2, $log2)." and ".pow(2, $log2 + 1).".\n";
        }

        $response->body = ob_get_clean();
        return $response;
    }
}
