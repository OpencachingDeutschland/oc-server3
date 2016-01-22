<?php

namespace okapi\views\devel\cronreport;

use Exception;
use okapi\Okapi;
use okapi\Cache;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\OkapiRedirectResponse;
use okapi\OkapiHttpResponse;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\cronjobs\CronJobController;

class View
{
    public static function call()
    {
        # This is a hidden page for OKAPI developers. It will output a cronjobs
        # report. This is useful for debugging.

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        ob_start();

        require_once($GLOBALS['rootpath']."okapi/cronjobs.php");
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
            if (!isset($schedule[$name]))
                print "NOT YET SCHEDULED\n";
            elseif ($schedule[$name] <= time())
                print "DELAYED: should be run ".(time() - $schedule[$name])." seconds ago\n";
            else
                print "scheduled to run in ".str_pad($schedule[$name] - time(), 6, " ", STR_PAD_LEFT)." seconds\n";
        }
        print "\n";
        print "Crontab last ping: ";
        if (Cache::get('crontab_last_ping'))
            print (time() - Cache::get('crontab_last_ping'))." seconds ago";
        else
            print "NEVER";
        print " (crontab_check_counter: ".Cache::get('crontab_check_counter').").\n";
        print "clog_revisions_daily: ";
        if (Cache::get('clog_revisions_daily'))
        {
            foreach (Cache::get('clog_revisions_daily') as $time => $rev)
                print "$rev ";
            print "\n";
        } else {
            print "NULL\n";
        }
        $response->body = ob_get_clean();
        return $response;
    }
}
