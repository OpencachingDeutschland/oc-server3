<?php

namespace okapi\CronJob;

use okapi\locale\Locales;
use okapi\Okapi;

/**
 * Once per week, check if all required locales are installed. If not,
 * keep nagging the admins to do so.
 */
class LocaleChecker extends Cron5Job
{
    public function get_period() { return 7*86400; }
    public function execute()
    {
        $required = Locales::get_required_locales();
        $installed = Locales::get_installed_locales();
        $missing = array();
        foreach ($required as $locale)
            if (!in_array($locale, $installed))
                $missing[] = $locale;
        if (count($missing) == 0)
            return; # okay!
        ob_start();
        print "Hi!\n\n";
        print "Your system is missing some locales required by OKAPI for proper\n";
        print "internationalization support. OKAPI comes with support for different\n";
        print "languages. This number (hopefully) will be growing.\n\n";
        print "Please take a moment to install the following missing locales:\n\n";
        $prefixes = array();
        foreach ($missing as $locale)
        {
            print " - ".$locale."\n";
            $prefixes[substr($locale, 0, 2)] = true;
        }
        $prefixes = array_keys($prefixes);
        print "\n";
        if ((count($missing) == 1) && ($missing[0] == 'POSIX'))
        {
            # I don't remember how to install POSIX, probably everyone has it anyway.
        }
        else
        {
            print "On Debian, try the following:\n\n";
            foreach ($prefixes as $lang)
            {
                if ($lang != 'PO') # Two first letters cut from POSIX.
                    print "sudo apt-get install language-pack-".$lang."-base\n";
            }
            print "sudo service apache2 restart\n";
            print "\n";
        }
        print "Thanks!\n\n";
        print "-- \n";
        print "OKAPI Team";
        Okapi::mail_admins("Additional setup needed: Missing locales.", ob_get_clean());
    }
}
