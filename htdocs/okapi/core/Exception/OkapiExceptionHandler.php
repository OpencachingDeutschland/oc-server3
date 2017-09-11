<?php

namespace okapi\core\Exception;

use okapi\core\Db;
use okapi\core\Okapi;
use okapi\Settings;

/** Container for exception-handling functions. */
class OkapiExceptionHandler
{
    /** Handle exception thrown while executing OKAPI request. */
    public static function handle($e)
    {
        if ($e instanceof OAuthServerException)
        {
            # This is thrown on invalid OAuth requests. There are many subclasses
            # of this exception. All of them result in HTTP 400 or HTTP 401 error
            # code. See also: https://oauth.net/core/1.0a/#http_codes

            if ($e instanceof OAuthServer400Exception)
                header("HTTP/1.0 400 Bad Request");
            else
                header("HTTP/1.0 401 Unauthorized");
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=utf-8");

            print $e->getOkapiJSON();
        }
        elseif ($e instanceof BadRequest)
        {
            # Intentionally thrown from within the OKAPI method code.
            # Consumer (aka external developer) had something wrong with his
            # request and we want him to know that.

            # headers may have been sent e.g. by views/update
            if (!headers_sent())
            {
                header("HTTP/1.0 400 Bad Request");
                header("Access-Control-Allow-Origin: *");
                header("Content-Type: application/json; charset=utf-8");
            }

            print $e->getOkapiJSON();
        }
        elseif ($e instanceof DbLockWaitTimeoutException)
        {
            # As long as it happens occasionally only, it is safe to silently cast
            # this error into a HTTP 503 response. (In the future, we might want to
            # measure the frequency of such errors too.)

            if (!headers_sent())
            {
                header("HTTP/1.0 503 Service Unavailable");
                header("Access-Control-Allow-Origin: *");
                header("Content-Type: application/json; charset=utf-8");
            }

            print json_encode(array("error" => array(
                'developer_message' => (
                    "OKAPI is experiencing an increased server load and cannot handle your ".
                    "request just now. Please repeat your request in a minute. If this ".
                    "problem persists, then please contact us at: ".
                    implode(", ", \get_admin_emails())
                )
            )));
        }
        else # (ErrorException, SQL syntax exception etc.)
        {
            # This one is thrown on PHP notices and warnings - usually this
            # indicates an error in OKAPI method. If thrown, then something
            # must be fixed on OUR part.

            if (!headers_sent())
            {
                header("HTTP/1.0 500 Internal Server Error");
                header("Access-Control-Allow-Origin: *");
                header("Content-Type: text/plain; charset=utf-8");
            }

            print "Oops... Something went wrong on *our* part.\n\n";
            print "Message was passed on to the site administrators. We'll try to fix it.\n";
            print "Contact the developers if you think you can help!";

            error_log($e->getMessage());

            $exception_info = self::get_exception_info($e);

            if (class_exists(Settings::class) && (Settings::get('DEBUG')))
            {
                print "\n\nBUT! Since the DEBUG flag is on, then you probably ARE a developer yourself.\n";
                print "Let's cut to the chase then:";
                print "\n\n".$exception_info;
            }
            if (class_exists(Settings::class) && (Settings::get('DEBUG_PREVENT_EMAILS')))
            {
                # Sending emails was blocked on admin's demand.
                # This is possible only on development environment.
            }
            else
            {
                $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'cli-execution';
                $subject = 'OKAPI Method Error - '.substr(
                        $requestUri, 0, strpos($requestUri.'?', '?')
                    );

                $message = (
                    "OKAPI caught the following exception while executing API method request.\n".
                    "This is an error in OUR code and should be fixed. Please contact the\n".
                    "developer of the module that threw this error. Thanks!\n\n".
                    $exception_info
                );
                try
                {
                    Okapi::mail_admins($subject, $message);
                }
                catch (\Exception $e)
                {
                    # Unable to use full-featured mail_admins version. We'll use a backup.
                    # We need to make sure we're not spamming.

                    $lock_file = "/tmp/okapi-fatal-error-mode";
                    $last_email = false;
                    if (file_exists($lock_file))
                        $last_email = filemtime($lock_file);
                    if ($last_email === false) {
                        # Assume this is the first email.
                        $last_email = 0;
                    }
                    if (time() - $last_email < 60) {
                        # Send no more than one per minute.
                        return;
                    }
                    @touch($lock_file);

                    $admin_email = implode(", ", \get_admin_emails());
                    $sender_email = class_exists(Settings::class) ? Settings::get('FROM_FIELD') : 'root@localhost';
                    $subject = "Fatal error mode: ".$subject;
                    $message = "Fatal error mode: OKAPI will send at most ONE message per minute.\n\n".$message;
                    $headers = (
                        "Content-Type: text/plain; charset=utf-8\n".
                        "From: OKAPI <$sender_email>\n".
                        "Reply-To: $sender_email\n"
                    );
                    mail($admin_email, $subject, $message, $headers);
                }
            }
        }
    }

    public static function removeSensitiveData($message)
    {
        return str_replace(
            array(
                Settings::get('DB_PASSWORD'),
                "'".Settings::get('DB_USERNAME')."'",
                Settings::get('DB_SERVER'),
                "'".Settings::get('DB_NAME')."'"
            ),
            array(
                "******",
                "'******'",
                "******",
                "'******'"
            ),
            $message
        );
    }

    public static function get_exception_info($e)
    {
        $exception_info = "===== ERROR MESSAGE =====\n"
            .get_class($e).":\n"
            .trim(self::removeSensitiveData($e->getMessage()))
            ."\n=========================\n\n";
        if ($e instanceof FatalError)
        {
            # This one doesn't have a stack trace. It is fed directly to OkapiExceptionHandler::handle
            # by OkapiErrorHandler::handle_shutdown. Instead of printing trace, we will just print
            # the file and line.

            $exception_info .= "File: ".$e->getFile()."\nLine: ".$e->getLine()."\n\n";
        }
        else
        {
            $exception_info .= "--- Stack trace ---\n".
                self::removeSensitiveData($e->getTraceAsString())."\n\n";

            if ($e instanceof DbLockWaitTimeoutException) {
                $exception_info .= "--- InnoDB status ---\n";
                try {
                    $exception_info .= Db::select_row("show engine innodb status")['Status'];
                } catch (\Exception $e2) {
                    $exception_info .= (
                        "Could not retrieve. Missing 'GRANT PROCESS'? Error was:\n".
                        $e2->getMessage()
                    );
                }
                $exception_info .= "\n\n";
            }
        }

        $exception_info .= (isset($_SERVER['REQUEST_URI']) ? "--- OKAPI method called ---\n".
            preg_replace("/([?&])/", "\n$1", $_SERVER['REQUEST_URI'])."\n\n" : "");
        $exception_info .= "--- OKAPI version ---\n".Okapi::$version_number.
            " (".Okapi::$git_revision.")\n\n";

        # This if-condition will solve some (but not all) problems when trying to execute
        # OKAPI code from command line;
        # see https://github.com/opencaching/okapi/issues/243.
        if (function_exists('getallheaders'))
        {
            $exception_info .= "--- Request headers ---\n".implode("\n", array_map(
                    function($k, $v) { return "$k: $v"; },
                    array_keys(getallheaders()), array_values(getallheaders())
                ));
        }

        return $exception_info;
    }
}
