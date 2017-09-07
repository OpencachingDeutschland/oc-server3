<?php

namespace okapi;

use okapi\Exception\FatalError;
use okapi\Exception\OkapiExceptionHandler;

/** Container for error-handling functions. */
class OkapiErrorHandler
{
    public static $treat_notices_as_errors = false;

    /** Handle error encountered while executing OKAPI request. */
    public static function handle($severity, $message, $filename, $lineno)
    {
        if ($severity == E_STRICT || $severity == E_DEPRECATED) return false;
        if (($severity == E_NOTICE) && !self::$treat_notices_as_errors) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $filename, $lineno);
    }

    /** Use this BEFORE calling a piece of buggy code. */
    public static function disable()
    {
        restore_error_handler();
    }

    /** Use this AFTER calling a piece of buggy code. */
    public static function reenable()
    {
        set_error_handler(array(__CLASS__, 'handle'));
    }

    /** Handle FATAL errors (not catchable, report only). */
    public static function handle_shutdown()
    {
        $error = error_get_last();

        # We don't know whether this error has been already handled. The error_get_last
        # function will return E_NOTICE or E_STRICT errors if the script has shut down
        # correctly. The only error which cannot be recovered from is E_ERROR, we have
        # to check the type then.

        if (($error !== null) && ($error['type'] == E_ERROR))
        {
            $e = new FatalError($error['message'], 0, $error['type'], $error['file'], $error['line']);
            OkapiExceptionHandler::handle($e);
        }
    }
}
