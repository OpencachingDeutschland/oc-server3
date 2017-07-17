<?php

namespace okapi\views\devel\cronreport;

use okapi\Cache;
use okapi\cronjobs\CronJobController;
use okapi\Okapi;
use okapi\OkapiHttpResponse;

class View
{
    public static function call()
    {
        # This is a hidden page for OKAPI developers. It will output a cronjobs
        # report. This is useful for debugging.

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        ob_start();

        require_once "okapi/cronjobs.php";
        $schedule = Cache::get("cron_schedule");
        if ($schedule == null)
            $schedule = array();
        print "Nearest event: ";
        if (Okapi::get_var('cron_nearest_event'))
            print "in ".(Okapi::get_var('cron_nearest_event') - time())." seconds.\n\n";
        else
            print "NOT SET\n\n";
        $cronjobs = CronJobController::get_enabled_cronjobs();
        usort($cronjobs, function($a, $b) {
            $cmp = function($a, $b) { return ($a < $b) ? -1 : (($a > $b) ? 1 : 0); };
            $by_type = $cmp($a->get_type(), $b->get_type());
            if ($by_type != 0)
                return $by_type;
            return $cmp($a->get_name(), $b->get_name());
        });
        print str_pad("TYPE", 11)."  ".str_pad("NAME", 40)."  SCHEDULE\n";
        print str_pad("----", 11)."  ".str_pad("----", 40)."  --------\n";
        foreach ($cronjobs as $cronjob)
        {
            $type = $cronjob->get_type();
            $name = $cronjob->get_name();
            print str_pad($type, 11)."  ".str_pad($name, 40)."  ";
            if (!isset($schedule[$name])) {
                print "NOT YET SCHEDULED";
            } elseif ($schedule[$name] <= time()) {
                print "DELAYED: should be run ".(time() - $schedule[$name])." seconds ago";
            } else {
                print "scheduled to run in ".str_pad($schedule[$name] - time(), 6, " ", STR_PAD_LEFT)." seconds";
            }
            if (isset($schedule[$name])) {
                $delta = abs(time() - $schedule[$name]);
                if ($delta > 10 * 60) {
                    print " (";
                    print substr(date('c', $schedule[$name]), 11, 8);
                    print ", ";
                    $datestr = substr(date('c', $schedule[$name]), 0, 10);
                    $today = substr(date('c', time()), 0, 10);
                    $tomorrow = substr(date('c', time() + 86400), 0, 10);
                    if ($datestr == $today) {
                        print "today";
                    } elseif ($datestr == $tomorrow) {
                        print "tomorrow";
                    } else {
                        print $datestr;
                    }
                    print ")";
                }
            }
            print "\n";
        }
        print "\n";
        print "Crontab last ping: ";
        if (Cache::get('crontab_last_ping'))
            print (time() - Cache::get('crontab_last_ping'))." seconds ago";
        else
            print "NEVER";
        print " (crontab_check_counter: ".Cache::get('crontab_check_counter').").\n";
        print "Debug clog_revisions_daily: ";
        if (Cache::get('clog_revisions_daily'))
        {
            $prev = null;
            foreach (Cache::get('clog_revisions_daily') as $time => $rev) {
                if ($prev != null) {
                    print "(+".($rev-$prev).") ";
                }
                print "$rev ";
                $prev = $rev;
            }
            print "\n";
        } else {
            print "NULL\n";
        }
        $response->body = ob_get_clean();
        return $response;
    }
}
