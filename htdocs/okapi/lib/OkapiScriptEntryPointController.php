<?php

namespace okapi\lib;

use okapi\Exception\Http404;
use okapi\Okapi;
use okapi\OkapiUrls;
use okapi\views\http404\View as Http404View;

class OkapiScriptEntryPointController
{
    public static function dispatch_request($uri)
    {
        # Chop off the ?args=... part.

        if (strpos($uri, '?') !== false)
            $uri = substr($uri, 0, strpos($uri, '?'));

        # Chop off everything before "/okapi/". This should work for okay for most "weird"
        # server configurations. It will also address a more subtle issue described here:
        # https://stackoverflow.com/questions/8040461/request-uri-unexpectedly-contains-fqdn

        if (strpos($uri, "/okapi/") !== false)
            $uri = substr($uri, strpos($uri, "/okapi/"));

        # Make sure we're in the right directory (.htaccess should make sure of that).

        if (strpos($uri, "/okapi/") !== 0)
            throw new \Exception("'$uri' is outside of the /okapi/ path.");
        $uri = substr($uri, 7);

        # Initializing internals and running pre-request cronjobs (we don't want
        # cronjobs to be run before "okapi/update", for example before database
        # was installed).

        $allow_cronjobs = ($uri != "update");
        Okapi::init_internals($allow_cronjobs);

        # Checking for allowed patterns...

        try
        {
            foreach (OkapiUrls::$mapping as $pattern => $namespace)
            {
                $matches = null;
                if (preg_match("#$pattern#", $uri, $matches))
                {
                    # Pattern matched! Moving on to the proper View...

                    array_shift($matches);
                    $response = call_user_func_array(array('\\okapi\\views\\'.
                        str_replace('/', '\\', $namespace).'\\View', 'call'), $matches);
                    if ($response)
                        $response->display();
                    return;
                }
            }
        }
        catch (Http404 $e)
        {
            /* pass */
        }

        # None of the patterns matched OR method threw the Http404 exception.

        $response = Http404View::call();
        $response->display();
    }
}
