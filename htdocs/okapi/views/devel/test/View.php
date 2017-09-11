<?php

namespace okapi\views\devel\test;

use Exception;
use okapi\core\Cache;
use okapi\core\Okapi;
use okapi\core\Response\OkapiHttpResponse;

class View
{
    public static function call()
    {
        # This is a hidden page for OKAPI developers. It will perform some
        # diagnostic tasks.

        # See https://github.com/opencaching/okapi/issues/506.
        # If someone misuses this hidden feature, we may need to disable it.

        $body = '';
        if (isset($_GET['adminmail'])) {
            Okapi::mail_admins(
                $_GET['adminmail'],
                "This is a manually generated test email for OKAPI admin notification.\n" .
                "It should reach all admins who are registerd in the 'ADMINS' field in\n" .
                "okapi_settings.php.\n"
            );
            $body .= "An email with subject '".$_GET['adminmail']."' has been sent to the site admins.\n";
        }

        if (isset($_GET['mail_admins_counter']))
        {
            # see Okapi::mail_admins
            # subject for method errors is eg. "OKAPI Method Error - /okapi/devel/test"

            $cache_key = 'mail_admins_counter/'.(floor(time() / 3600) * 3600).'/'.md5($_GET['mail_admins_counter']);
            try {
                $counter = Cache::get($cache_key);
                $body .=
                    "The mail counter for subject '".$_GET['mail_admins_counter']."' is " .
                    ($counter === null ? 'null' : $counter) . "\n";
            } catch (Exception $e) {
                $body .= "Error while retrieving the mail counter: ". $e->getMessage();
            }
        }

        if (isset($_GET['exception'])) {
            throw new Exception("Testing OKAPI exception handling. " . $_GET['exception']);
        }

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        $response->body = $body;

        return $response;
    }
}
