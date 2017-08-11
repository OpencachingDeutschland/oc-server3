<?php

namespace okapi;

use Exception;

#
# All HTTP requests within the /okapi/ path are redirected through this
# controller. From here we'll pass them to the right entry point (or
# display an appropriate error message).
#
# To learn more about OKAPI, see core.php.
#

# -------------------------

#
# Set up the rootpath. If OKAPI is called via its Facade entrypoint, then this
# variable is being set up by the OC site. If it is called via the controller
# endpoint (this one!), then we need to set it up ourselves.
#

$GLOBALS['rootpath'] = __DIR__.'/../';

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core.php';

OkapiErrorHandler::$treat_notices_as_errors = true;

if (ob_list_handlers() === ['default output handler']) {
    # We will assume that this one comes from "output_buffering" being turned on
    # in PHP config. This is very common and probably is good for most other OC
    # pages. But we don't need it in OKAPI. We will just turn this off.

    ob_end_clean();
}


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
            throw new Exception("'$uri' is outside of the /okapi/ path.");
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
                    require_once __DIR__ . "/views/$namespace.php";
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

        require_once __DIR__ . '/views/http404.php';
        $response = \okapi\views\http404\View::call();
        $response->display();
    }
}

Okapi::gettext_domain_init();
OkapiScriptEntryPointController::dispatch_request($_SERVER['REQUEST_URI']);
Okapi::gettext_domain_restore();
