<?php

namespace okapi;

# OKAPI Framework -- Wojciech Rygielski <rygielski@mimuw.edu.pl>

# If you want to include_once/require_once OKAPI in your code,
# see facade.php. You should not rely on any other file, never!

use Exception;
use ErrorException;
use ArrayObject;
use okapi\oauth\OAuthServerException;
use okapi\oauth\OAuthServer400Exception;
use okapi\oauth\OAuthServer401Exception;
use okapi\oauth\OAuthMissingParameterException;
use okapi\oauth\OAuthConsumer;
use okapi\oauth\OAuthToken;
use okapi\oauth\OAuthServer;
use okapi\oauth\OAuthSignatureMethod_HMAC_SHA1;
use okapi\oauth\OAuthRequest;
use okapi\cronjobs\CronJobController;

/** Return an array of email addresses which always get notified on OKAPI errors. */
function get_admin_emails()
{
    $emails = array();
    if (class_exists("okapi\\Settings"))
    {
        try
        {
            foreach (Settings::get('ADMINS') as $email)
                if (!in_array($email, $emails))
                    $emails[] = $email;
        }
        catch (Exception $e) { /* pass */ }
    }
    if (count($emails) == 0)
        $emails[] = 'root@localhost';
    return $emails;
}

#
# Base exception types.
#

/** A base class for all bad request exceptions. */
class BadRequest extends Exception {
    protected function provideExtras(&$extras) {
        $extras['reason_stack'][] = 'bad_request';
        $extras['status'] = 400;
    }
    public function getOkapiJSON() {
        $extras = array(
            'developer_message' => $this->getMessage(),
            'reason_stack' => array(),
        );
        $this->provideExtras($extras);
        $extras['more_info'] = Settings::get('SITE_URL')."okapi/introduction.html#errors";
        return json_encode(array("error" => $extras));
    }
}

/** Thrown on PHP's FATAL errors (detected in a shutdown function). */
class FatalError extends ErrorException {}

#
# We'll try to make PHP into something more decent. Exception and
# error handling.
#

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
            # code. See also: http://oauth.net/core/1.0a/#http_codes

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
        else # (ErrorException, MySQL exception etc.)
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

            if (class_exists("okapi\\Settings") && (Settings::get('DEBUG')))
            {
                print "\n\nBUT! Since the DEBUG flag is on, then you probably ARE a developer yourself.\n";
                print "Let's cut to the chase then:";
                print "\n\n".$exception_info;
            }
            if (class_exists("okapi\\Settings") && (Settings::get('DEBUG_PREVENT_EMAILS')))
            {
                # Sending emails was blocked on admin's demand.
                # This is possible only on development environment.
            }
            else
            {
                $subject = "OKAPI Method Error - ".substr(
                    $_SERVER['REQUEST_URI'], 0, strpos(
                    $_SERVER['REQUEST_URI'].'?', '?'));

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
                catch (Exception $e)
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

                    $admin_email = implode(", ", get_admin_emails());
                    $sender_email = class_exists("okapi\\Settings") ? Settings::get('FROM_FIELD') : 'root@localhost';
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
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }

    /** Use this BEFORE calling a piece of buggy code. */
    public static function disable()
    {
        restore_error_handler();
    }

    /** Use this AFTER calling a piece of buggy code. */
    public static function reenable()
    {
        set_error_handler(array('\okapi\OkapiErrorHandler', 'handle'));
    }

    /** Handle FATAL errors (not catchable, report only). */
    public static function handle_shutdown()
    {
        $error = error_get_last();

        # We don't know whether this error has been already handled. The error_get_last
        # function will return E_NOTICE or E_STRICT errors if the stript has shut down
        # correctly. The only error which cannot be recovered from is E_ERROR, we have
        # to check the type then.

        if (($error !== null) && ($error['type'] == E_ERROR))
        {
            $e = new FatalError($error['message'], 0, $error['type'], $error['file'], $error['line']);
            OkapiExceptionHandler::handle($e);
        }
    }
}

# Setting handlers. Errors will now throw exceptions, and all exceptions
# will be properly handled. (Unfortunetelly, only SOME errors can be caught
# this way, PHP limitations...)

set_exception_handler(array('\okapi\OkapiExceptionHandler', 'handle'));
set_error_handler(array('\okapi\OkapiErrorHandler', 'handle'));
register_shutdown_function(array('\okapi\OkapiErrorHandler', 'handle_shutdown'));

#
# Extending exception types (introducing some convenient shortcuts for
# the developer).
#

class Http404 extends BadRequest {}

/** Common type of BadRequest: Required parameter is missing. */
class ParamMissing extends BadRequest
{
    private $paramName;
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'missing_parameter';
        $extras['parameter'] = $this->paramName;
    }
    public function __construct($paramName)
    {
        parent::__construct("Required parameter '$paramName' is missing.");
        $this->paramName = $paramName;
    }
}

/** Common type of BadRequest: Parameter has invalid value. */
class InvalidParam extends BadRequest
{
    public $paramName;

    /** What was wrong about the param? */
    public $whats_wrong_about_it;

    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'invalid_parameter';
        $extras['parameter'] = $this->paramName;
        $extras['whats_wrong_about_it'] = $this->whats_wrong_about_it;
    }
    public function __construct($paramName, $whats_wrong_about_it = "", $code = 0)
    {
        $this->paramName = $paramName;
        $this->whats_wrong_about_it = $whats_wrong_about_it;
        if ($whats_wrong_about_it)
            parent::__construct("Parameter '$paramName' has invalid value: ".$whats_wrong_about_it, $code);
        else
            parent::__construct("Parameter '$paramName' has invalid value.", $code);
    }
}

/** Thrown on invalid SQL queries. */
class DbException extends Exception {}

#
# Database access abstraction layer.
#

/**
 * Database access abstraction layer class. Use this instead of "raw" mysql_*,
 * mysqli_* and PDO functions.
 */
class Db
{
    private static $connected = false;

    public static function connect()
    {
        if (mysql_connect(Settings::get('DB_SERVER'), Settings::get('DB_USERNAME'), Settings::get('DB_PASSWORD')))
        {
            mysql_select_db(Settings::get('DB_NAME'));
            mysql_query("set names '" . Settings::get('DB_CHARSET') . "'");
            self::$connected = true;
        }
        else
            throw new Exception("Could not connect to MySQL: ".mysql_error());
    }

    /** Fetch [{row}], return {row}. */
    public static function select_row($query)
    {
        $rows = self::select_all($query);
        switch (count($rows))
        {
            case 0: return null;
            case 1: return $rows[0];
            default:
                throw new DbException("Invalid query. Db::select_row returned more than one row for:\n\n".$query."\n");
        }
    }

    /** Fetch all [{row}, {row}], return [{row}, {row}]. */
    public static function select_all($query)
    {
        $rows = array();
        self::select_and_push($query, $rows);
        return $rows;
    }

    /** Private. */
    private static function select_and_push($query, & $arr, $keyField = null)
    {
        $rs = self::query($query);
        while (true)
        {
            $row = Db::fetch_assoc($rs);
            if ($row === false)
                break;
            if ($keyField == null)
                $arr[] = $row;
            else
                $arr[$row[$keyField]] = $row;
        }
        Db::free_result($rs);
    }

    /** Fetch all [(A,A), (A,B), (B,A)], return {A: [{row}, {row}], B: [{row}]}. */
    public static function select_group_by($keyField, $query)
    {
        $groups = array();
        $rs = self::query($query);
        while (true)
        {
            $row = Db::fetch_assoc($rs);
            if ($row === false)
                break;
            $groups[$row[$keyField]][] = $row;
        }
        Db::free_result($rs);
        return $groups;
    }

    /** Fetch [(A)], return A. */
    public static function select_value($query)
    {
        $column = self::select_column($query);
        if ($column == null)
            return null;
        if (count($column) == 1)
            return $column[0];
        throw new DbException("Invalid query. Db::select_value returned more than one row for:\n\n".$query."\n");
    }

    /** Fetch all [(A), (B), (C)], return [A, B, C]. */
    public static function select_column($query)
    {
        $column = array();
        $rs = self::query($query);
        while (true)
        {
            $values = Db::fetch_row($rs);
            if ($values === false)
                break;
            array_push($column, $values[0]);
        }
        Db::free_result($rs);
        return $column;
    }

    public static function last_insert_id()
    {
        return mysql_insert_id();
    }

    public static function fetch_assoc($rs)
    {
        return mysql_fetch_assoc($rs);
    }

    public static function fetch_row($rs)
    {
        return mysql_fetch_row($rs);
    }

    public static function fetch_array($rs)
    {
        return mysql_fetch_array($rs);
    }

    public static function free_result($rs)
    {
        return mysql_free_result($rs);
    }

    public static function escape_string($value)
    {
        return mysql_real_escape_string($value);
    }

    public static function execute($query)
    {
        $rs = self::query($query);
        if ($rs !== true)
            throw new DbException("Db::execute returned a result set for your query. ".
                "You should use Db::select_* or Db::query for SELECT queries!");
    }

    public static function query($query)
    {
        if (!self::$connected)
            self::connect();
        $rs = mysql_query($query);
        if (!$rs)
        {
            $errno = mysql_errno();
            $msg = mysql_error();

            /* Detect issue #340 and try to repair... */

            if (in_array($errno, array(144, 130)) && strstr($msg, "okapi_cache")) {

                /* MySQL claims that is tries to repair it automatically. We'll
                 * try outselves. */

                try {
                    self::execute("repair table okapi_cache");
                    Okapi::mail_admins(
                        "okapi_cache - Automatic repair",
                        "Hi.\n\nOKAPI detected that okapi_cache table needed ".
                        "repairs and it has performed such\nrepairs automatically. ".
                        "However, this should not happen regularly!"
                    );
                } catch (Exception $e) {

                    /* Last resort. */

                    try {
                        self::execute("truncate okapi_cache");
                        Okapi::mail_admins(
                            "okapi_cache was truncated",
                            "Hi.\n\nOKAPI detected that okapi_cache table needed ".
                            "repairs, but it failed to repair\nthe table automatically. ".
                            "In order to counteract more severe errors, \nwe have ".
                            "truncated the okapi_cache table to make it alive.\n".
                            "However, this should not happen regularly!"
                        );
                    } catch (Exception $e) {
                        # pass
                    }
                }
            }

            throw new DbException("SQL Error $errno: $msg\n\nThe query was:\n".$query."\n");
        }
        return $rs;
    }

    /**
     * Return number of rows actually updated, inserted or deleted by the last
     * statement executed with execute(). It DOES NOT return number of rows
     * returned by the last select statement.
     */
    public static function get_affected_row_count()
    {
        return mysql_affected_rows();
    }

    public static function field_exists($table, $field)
    {
        if (!preg_match("/[a-z0-9_]+/", $table.$field))
            return false;
        try {
            $spec = self::select_all("desc ".$table.";");
        } catch (Exception $e) {
            /* Table doesn't exist, probably. */
            return false;
        }
        foreach ($spec as &$row_ref) {
            if (strtoupper($row_ref['Field']) == strtoupper($field))
                return true;
        }
        return false;
    }
}

#
# Including OAuth internals. Preparing OKAPI Consumer and Token classes.
#

require_once($GLOBALS['rootpath']."okapi/oauth.php");

class OkapiConsumer extends OAuthConsumer
{
    public $name;
    public $url;
    public $email;

    /**
     * A set of binary flags indicating "special permissions".
     *
     * Some chosen Consumers gain special permissions within OKAPI. These
     * permissions are set by direct SQL UPDATEs in the database, and are not
     * part of the official documentation, nor are they backward-compatible.
     *
     * Before you grant any of these permissions to any Consumer, make him
     * aware, that he may loose them at any time (e.g. after OKAPI update)!
     */
    private $bflags;

    /**
     * Allows the consumer to set higher values on the "limit" parameters of
     * some methods.
     */
    const FLAG_SKIP_LIMITS = 1;

    /**
     * Allows the consumer to call the "services/caches/map/tile" method.
     */
    const FLAG_MAPTILE_ACCESS = 2;

    /**
     * Marks the consumer key as 'revoked', i.e. disables the consumer.
     */
    const FLAG_KEY_REVOKED = 4;

    public function __construct($key, $secret, $name, $url, $email, $bflags=0)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->name = $name;
        $this->url = $url;
        $this->email = $email;
        $this->bflags = $bflags;
    }

    /**
     * Returns true if the consumer has the given flag set. See class contants
     * for the list of available flags.
     */
    public function hasFlag($flag)
    {
        return ($this->bflags & $flag) > 0;
    }

    public function __toString()
    {
        return "OkapiConsumer[key=$this->key,name=$this->name]";
    }
}

/**
 * Use this when calling OKAPI methods internally from OKAPI code. (If you want call
 * OKAPI from other OC code, you must use Facade class - see facade.php)
 */
class OkapiInternalConsumer extends OkapiConsumer
{
    public function __construct()
    {
        $admins = get_admin_emails();
        parent::__construct('internal', null, "Internal OKAPI jobs", null, $admins[0]);
    }
}

/**
 * Used when debugging methods using DEBUG_AS_USERNAME flag.
 */
class OkapiDebugConsumer extends OkapiConsumer
{
    public function __construct()
    {
        $admins = get_admin_emails();
        parent::__construct('debug', null, "DEBUG_AS_USERNAME Debugger", null, $admins[0]);
    }
}

/**
 * Used by calls made via Facade class. SHOULD NOT be referenced anywhere else from
 * within OKAPI code.
 */
class OkapiFacadeConsumer extends OkapiConsumer
{
    public function __construct()
    {
        $admins = get_admin_emails();
        parent::__construct('facade', null, "Internal usage via Facade", null, $admins[0]);
    }
}

class OkapiToken extends OAuthToken
{
    public $consumer_key;
    public $token_type;

    public function __construct($key, $secret, $consumer_key, $token_type)
    {
        parent::__construct($key, $secret);
        $this->consumer_key = $consumer_key;
        $this->token_type = $token_type;
    }
}
class OkapiRequestToken extends OkapiToken
{
    public $callback_url;
    public $authorized_by_user_id;
    public $verifier;

    public function __construct($key, $secret, $consumer_key, $callback_url,
        $authorized_by_user_id, $verifier)
    {
        parent::__construct($key, $secret, $consumer_key, 'request');
        $this->callback_url = $callback_url;
        $this->authorized_by_user_id = $authorized_by_user_id;
        $this->verifier = $verifier;
    }
}

class OkapiAccessToken extends OkapiToken
{
    public $user_id;

    public function __construct($key, $secret, $consumer_key, $user_id)
    {
        parent::__construct($key, $secret, $consumer_key, 'access');
        $this->user_id = $user_id;
    }
}

/** Use this in conjunction with OkapiInternalConsumer. */
class OkapiInternalAccessToken extends OkapiAccessToken
{
    public function __construct($user_id)
    {
        parent::__construct('internal-'.$user_id, null, 'internal', $user_id);
    }
}

/** Use this in conjunction with OkapiFacadeConsumer. */
class OkapiFacadeAccessToken extends OkapiAccessToken
{
    public function __construct($user_id)
    {
        parent::__construct('facade-'.$user_id, null, 'facade', $user_id);
    }
}

/** Used when debugging with DEBUG_AS_USERNAME. */
class OkapiDebugAccessToken extends OkapiAccessToken
{
    public function __construct($user_id)
    {
        parent::__construct('debug-'.$user_id, null, 'debug', $user_id);
    }
}

/** Default OAuthServer with some OKAPI-specific methods added. */
class OkapiOAuthServer extends OAuthServer
{
    public function __construct($data_store)
    {
        parent::__construct($data_store);
        # We want HMAC_SHA1 authorization method only.
        $this->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
    }

    /**
     * By default, works like verify_request, but it does support some additional
     * options. If $token_required == false, it doesn't throw an exception when
     * there is no token specified. You may also change the token_type required
     * for this request.
     */
    public function verify_request2(&$request, $token_type = 'access', $token_required = true)
    {
        $this->get_version($request);
        $consumer = $this->get_consumer($request);
        try {
            $token = $this->get_token($request, $consumer, $token_type);
        } catch (OAuthMissingParameterException $e) {
            # Note, that exception will be different if token is supplied
            # and is invalid. We catch only a completely MISSING token parameter.
            if (($e->getParamName() == 'oauth_token') && (!$token_required))
                $token = null;
            else
                throw $e;
        }
        $this->check_signature($request, $consumer, $token);
        return array($consumer, $token);
    }
}

# Including local datastore and settings (connecting SQL database etc.).

require_once($GLOBALS['rootpath']."okapi/settings.php");
require_once($GLOBALS['rootpath']."okapi/datastore.php");

class OkapiHttpResponse
{
    public $status = "200 OK";
    public $cache_control = "no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0";
    public $content_type = "text/plain; charset=utf-8";
    public $content_disposition = null;
    public $allow_gzip = true;
    public $connection_close = false;
    public $etag = null;

    /** Use this only as a setter, use get_body or print_body for reading! */
    public $body;

    /** This could be set in case when body is a stream of known length. */
    public $stream_length = null;

    public function get_length()
    {
        if (is_resource($this->body))
            return $this->stream_length;
        return strlen($this->body);
    }

    /** Note: You can call this only once! */
    public function print_body()
    {
        if (is_resource($this->body))
        {
            while (!feof($this->body))
                print fread($this->body, 1024*1024);
        }
        else
            print $this->body;
    }

    /**
     * Note: You can call this only once! The result might be huge (a stream),
     * it is usually better to print it directly with ->print_body().
     */
    public function get_body()
    {
        if (is_resource($this->body))
        {
            ob_start();
            fpassthru($this->body);
            return ob_get_clean();
        }
        else
            return $this->body;
    }

    /**
     * Print the headers and the body. This should be the last thing your script does.
     */
    public function display()
    {
        header("HTTP/1.1 ".$this->status);
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: ".$this->content_type);
        header("Cache-Control: ".$this->cache_control);
        if ($this->connection_close)
            header("Connection: close");
        if ($this->content_disposition)
            header("Content-Disposition: ".$this->content_disposition);
        if ($this->etag)
            header("ETag: $this->etag");

        # Make sure that gzip is supported by the client.
        $use_gzip = $this->allow_gzip;
        if (empty($_SERVER["HTTP_ACCEPT_ENCODING"]) || (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") === false))
            $use_gzip = false;

        # We will gzip the data ourselves, while disabling gziping by Apache. This way, we can
        # set the Content-Length correctly which is handy in some scenarios.

        if ($use_gzip && is_string($this->body))
        {
            # Apache won't gzip a response which is already gzipped.

            header("Content-Encoding: gzip");
            $gzipped = gzencode($this->body, 5);
            header("Content-Length: ".strlen($gzipped));
            print $gzipped;
        }
        else
        {
            # We don't want Apache to gzip this response. Tell it so.

            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', 1);
            }

            $length = $this->get_length();
            if ($length)
                header("Content-Length: ".$length);
            $this->print_body();
        }
    }
}

class OkapiRedirectResponse extends OkapiHttpResponse
{
    public $url;
    public function __construct($url) { $this->url = $url; }
    public function display()
    {
        header("HTTP/1.1 303 See Other");
        header("Location: ".$this->url);
    }
}

require_once ($GLOBALS['rootpath'].'okapi/lib/tbszip.php');
use \clsTbsZip;

class OkapiZIPHttpResponse extends OkapiHttpResponse
{
    public $zip;

    public function __construct()
    {
        $this->zip = new clsTbsZip();
        $this->zip->CreateNew();
    }

    public function print_body()
    {
        $this->zip->Flush(clsTbsZip::TBSZIP_DOWNLOAD|clsTbsZip::TBSZIP_NOHEADER);
    }

    public function get_body()
    {
        $this->zip->Flush(clsTbsZip::TBSZIP_STRING);
        return $this->zip->OutputSrc;
    }

    public function get_length()
    {
        # The _EstimateNewArchSize() method returns *false* if archive
        # size can not be calculated *exactly*, which causes display()
        # method to skip Content-Length header, and triggers chunking
        return $this->zip->_EstimateNewArchSize();
    }

    public function display()
    {
        $this->allow_gzip = false;
        parent::display();
    }
}

class OkapiLock
{
    private $lockfile;
    private $lock;

    /** Note: This does NOT tell you if someone currently locked it! */
    public static function exists($name)
    {
        $lockfile = Okapi::get_var_dir()."/okapi-lock-".$name;
        return file_exists($lockfile);
    }

    public static function get($name)
    {
        return new OkapiLock($name);
    }

    private function __construct($name)
    {
        if (Settings::get('DEBUG_PREVENT_SEMAPHORES'))
        {
            # Using semaphores is forbidden on this server by its admin.
            # This is possible only on development environment.
            $this->lock = null;
        }
        else
        {
            $this->lockfile = Okapi::get_var_dir()."/okapi-lock-".$name;
            $this->lock = fopen($this->lockfile, "wb");
        }
    }

    public function acquire()
    {
        if ($this->lock !== null)
            flock($this->lock, LOCK_EX);
    }

    public function try_acquire()
    {
        if ($this->lock !== null)
            return flock($this->lock, LOCK_EX | LOCK_NB);
        else
            return true;  # $lock can be null only when debugging
    }

    public function release()
    {
        if ($this->lock !== null)
            flock($this->lock, LOCK_UN);
    }

    /**
     * Use this method clean up obsolete and *unused* lock names (usually there
     * is no point in removing locks that can be reused.
     */
    public function remove()
    {
        if ($this->lock !== null)
        {
            fclose($this->lock);
            unlink($this->lockfile);
        }
    }
}

/** Container for various OKAPI functions. */
class Okapi
{
    public static $data_store;
    public static $server;

    /* These two get replaced in automatically deployed packages. */
    public static $version_number = 1285;
    public static $git_revision = '0b41dc2d3545c97ff5b9494808b0b9f82db105c6';

    private static $okapi_vars = null;

    /** Return a new, random UUID. */
    public static function create_uuid()
    {
        /* If we're on Linux, then we'll use a system function for that. */

        if (file_exists("/proc/sys/kernel/random/uuid")) {
            return trim(file_get_contents("/proc/sys/kernel/random/uuid"));
        }

        /* On other systems (as well as on some other Linux distributions)
         * fall back to the original implementation (which is NOT safe - we had
         * one duplicate during 3 years of its running). */

        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /** Get a variable stored in okapi_vars. If variable not found, return $default. */
    public static function get_var($varname, $default = null)
    {
        if (self::$okapi_vars === null)
        {
            $rs = Db::query("
                select var, value
                from okapi_vars
            ");
            self::$okapi_vars = array();
            while ($row = Db::fetch_assoc($rs))
                self::$okapi_vars[$row['var']] = $row['value'];
        }
        if (isset(self::$okapi_vars[$varname]))
            return self::$okapi_vars[$varname];
        return $default;
    }

    /**
     * Save a variable to okapi_vars. WARNING: The entire content of okapi_vars table
     * is loaded on EVERY execution. Do not store data in this table, unless it's
     * frequently needed.
     */
    public static function set_var($varname, $value)
    {
        Okapi::get_var($varname);
        Db::execute("
            replace into okapi_vars (var, value)
            values (
                '".Db::escape_string($varname)."',
                '".Db::escape_string($value)."');
        ");
        self::$okapi_vars[$varname] = $value;
    }

    /**
     * Remove database passwords and other sensitive data from the given
     * message (which is usually a trace, var_dump or print_r output).
     */
    public static function removeSensitiveData($message)
    {
        # This method is initially defined in the OkapiExceptionHandler class,
        # so that it is accessible even before the Okapi class is initialized.

        return OkapiExceptionHandler::removeSensitiveData($message);
    }

    /** Send an email message to local OKAPI administrators. */
    public static function mail_admins($subject, $message)
    {
        # Make sure we're not sending HUGE emails.

        if (strlen($message) > 10000) {
            $message = substr($message, 0, 10000)."\n\n...(message clipped at 10k chars)\n";
        }

        # Make sure we're not spamming.

        $cache_key = 'mail_admins_counter/'.(floor(time() / 3600) * 3600).'/'.md5($subject);
        try {
            $counter = Cache::get($cache_key);
        } catch (DbException $e) {
            # This exception can occur during OKAPI update (#156), or when
            # the cache table is broken (#340). I am not sure which option is
            # better: 1. notify the admins about the error and risk spamming
            # them, 2. don't notify and don't risk spamming them. Currently,
            # I choose option 2.

            return;
        }
        if ($counter === null)
            $counter = 0;
        $counter++;
        try {
            Cache::set($cache_key, $counter, 3600);
        } catch (DbException $e) {
            # If `get` suceeded and `set` did not, then probably we're having
            # issue #156 scenario. We can ignore it here.
        }
        if ($counter <= 5)
        {
            # We're not spamming yet.

            self::mail_from_okapi(get_admin_emails(), $subject, $message);
        }
        else
        {
            # We are spamming. Prevent sending more emails.

            $content_cache_key_prefix = 'mail_admins_spam/'.(floor(time() / 3600) * 3600).'/';
            $timeout = 86400;
            if ($counter == 6)
            {
                self::mail_from_okapi(get_admin_emails(), "Anti-spam mode activated for '$subject'",
                    "OKAPI has activated an \"anti-spam\" mode for the following subject:\n\n".
                    "\"$subject\"\n\n".
                    "Anti-spam mode is activiated when more than 5 messages with\n".
                    "the same subject are sent within one hour.\n\n".
                    "Additional debug information:\n".
                    "- counter cache key: $cache_key\n".
                    "- content prefix: $content_cache_key_prefix<n>\n".
                    "- content timeout: $timeout\n"
                );
            }
            $content_cache_key = $content_cache_key_prefix.$counter;
            Cache::set($content_cache_key, $message, $timeout);
        }
    }

    /** Send an email message from OKAPI to the given recipients. */
    public static function mail_from_okapi($email_addresses, $subject, $message)
    {
        if (class_exists("okapi\\Settings") && (Settings::get('DEBUG_PREVENT_EMAILS')))
        {
            # Sending emails was blocked on admin's demand.
            # This is possible only on development environment.
            return;
        }
        if (!is_array($email_addresses))
            $email_addresses = array($email_addresses);
        $sender_email = class_exists("okapi\\Settings") ? Settings::get('FROM_FIELD') : 'root@localhost';
        mail(implode(", ", $email_addresses), $subject, $message,
            "Content-Type: text/plain; charset=utf-8\n".
            "From: OKAPI <$sender_email>\n".
            "Reply-To: $sender_email\n"
            );
    }

    /** Get directory to store dynamic (cache or temporary) files. No trailing slash included. */
    public static function get_var_dir()
    {
        $dir = Settings::get('VAR_DIR');
        if ($dir != null)
            return rtrim($dir, "/");
        throw new Exception("You need to set a valid VAR_DIR.");
    }

    /** Returns something like "Opencaching.PL" or "Opencaching.DE". */
    public static function get_normalized_site_name($site_url = null)
    {
        if ($site_url == null)
            $site_url = Settings::get('SITE_URL');
        $matches = null;
        if (preg_match("#^https?://(www.)?opencaching.([a-z.]+)/$#", $site_url, $matches)) {
            return "Opencaching.".strtoupper($matches[2]);
        } else {
            return "DEVELSITE";
        }
    }

    /**
     * Return a "schema code" of this OC site.
     *
     * While there are only two primary OC_BRANCHes (OCPL and OCDE), sites
     * based on the same branch may have a different schema of attributes,
     * cache types, log types, or even database structures. This method returns
     * a unique internal code which identifies a set of sites that share the
     * same schema. As all OCPL-based sites currently have different attribute
     * sets, there is a separate schema for each OCPL site.
     *
     * These values are used internally only, they SHOULD NOT be exposed to
     * external developers!
     */
    public static function get_oc_schema_code()
    {
        /* All OCDE-based sites use exactly the same schema. */

        if (Settings::get('OC_BRANCH') == 'oc.de') {
            return "OCDE";  // OC
        }

        /* All OCPL-based sites use separate schemas. (Hopefully, this will
         * change in time.) */

        $mapping = array(
            2 => "OCPL",  // OP
            6 => "OCORGUK",  // OK
            10 => "OCUS",  // OU
            14 => "OCNL",  // OB
            16 => "OCRO",  // OR
            // should be expanded when new OCPL-based sites are added
        );
        $oc_node_id = Settings::get("OC_NODE_ID");
        if (isset($mapping[$oc_node_id])) {
            return $mapping[$oc_node_id];
        } else {
            return "OTHER";
        }
    }

    /**
     * Return the recommended okapi_base_url.
     *
     * This is the URL which we want all *new* client applications to use.
     * OKAPI will suggest URLs with this prefix in various context, e.g. in all
     * the dynamically generated docs.
     *
     * Also see `get_allowed_base_urls` method.
     */
    public static function get_recommended_base_url()
    {
        return Settings::get('SITE_URL')."okapi/";
    }

    /**
     * Return a list of okapi_base_urls allowed to be used when calling OKAPI
     * methods in this installation.
     *
     * Since issue #416, the "recommended" okapi_base_url is *not* the only one
     * allowed (actually, there were more allowed before issue #416, but they
     * weren't allowed "officially").
     */
    public static function get_allowed_base_urls()
    {
        /* Currently, there are no config settings which would let us allow
         * to determine the proper values for this list. So, we need to have it
         * hardcoded. (Perhaps we should move this to etc/installations.xml?
         * But this wouldn't be efficient...)
         *
         * TODO: Replace "self::get_oc_schema_code()" by something better.
         *       Base URls depend on installations, not on schemas.
         */

        switch (self::get_oc_schema_code()) {
            case 'OCPL':
                $urls = array(
                    "http://opencaching.pl/okapi/",
                    "http://www.opencaching.pl/okapi/",
                );
                break;
            case 'OCDE':
                if (in_array(Settings::get('OC_NODE_ID'), array(4,5))) {
                    $urls = array(
                        preg_replace("/^https:/", "http:", Settings::get('SITE_URL')) . 'okapi/',
                        preg_replace("/^http:/", "https:", Settings::get('SITE_URL')) . 'okapi/',
                    );
                } else {
                    $urls = array(
                        "http://www.opencaching.de/okapi/",
                        "https://www.opencaching.de/okapi/",
                    );
                }
                break;
            case 'OCNL':
                $urls = array(
                    "http://www.opencaching.nl/okapi/",
                );
                break;
            case 'OCRO':
                $urls = array(
                    "http://www.opencaching.ro/okapi/",
                );
                break;
            case 'OCORGUK':
                $urls = array(
                    "http://www.opencaching.org.uk/okapi/",
                );
                break;
            case 'OCUS':
                $urls = array(
                    "http://www.opencaching.us/okapi/",
                    "http://opencaching.us/okapi/",
                );
                break;
            default:
                /* Unknown site. No extra allowed URLs. */
                $urls = array();
        }

        if (!in_array(self::get_recommended_base_url(), $urls)) {
            $urls[] = self::get_recommended_base_url();
        }

        return $urls;
    }

    /**
     * Pick text from $langdict based on language preference $langpref.
     *
     * Example:
     * pick_best_language(
     *   array('pl' => 'X', 'de' => 'Y', 'en' => 'Z'),
     *   array('sp', 'de', 'en')
     * ) == 'Y'.
     *
     * @param array $langdict - assoc array of lang-code => text.
     * @param array $langprefs - list of lang codes, in order of preference.
     */
    public static function pick_best_language($langdict, $langprefs)
    {
        foreach ($langprefs as $pref)
            if (isset($langdict[$pref]))
                return $langdict[$pref];
        foreach ($langdict as &$text_ref)
            return $text_ref;
        return "";
    }

    /**
     * Split the array into groups of max. $size items.
     */
    public static function make_groups($array, $size)
    {
        $i = 0;
        $groups = array();
        while ($i < count($array))
        {
            $groups[] = array_slice($array, $i, $size);
            $i += $size;
        }
        return $groups;
    }

    /**
     * Check if any pre-request cronjobs are scheduled to execute and execute
     * them if needed. Reschedule for new executions.
     */
    public static function execute_prerequest_cronjobs()
    {
        $nearest_event = Okapi::get_var("cron_nearest_event");
        if ($nearest_event + 0 <= time())
        {
            require_once($GLOBALS['rootpath']."okapi/cronjobs.php");
            $nearest_event = CronJobController::run_jobs('pre-request');
            Okapi::set_var("cron_nearest_event", $nearest_event);
        }
    }

    /**
     * Check if any cron-5 cronjobs are scheduled to execute and execute
     * them if needed. Reschedule for new executions.
     */
    public static function execute_cron5_cronjobs()
    {
        $nearest_event = Okapi::get_var("cron_nearest_event");
        if ($nearest_event + 0 <= time())
        {
            set_time_limit(0);
            ignore_user_abort(true);
            require_once($GLOBALS['rootpath']."okapi/cronjobs.php");
            $nearest_event = CronJobController::run_jobs('cron-5');
            Okapi::set_var("cron_nearest_event", $nearest_event);
        }
    }

    private static function gettext_set_lang($langprefs)
    {
        static $gettext_last_used_langprefs = null;
        static $gettext_last_set_locale = null;

        # We remember the last $langprefs argument which we've been called with.
        # This way, we don't need to call the actual locale-switching code most
        # of the times.

        if ($gettext_last_used_langprefs != $langprefs)
        {
            $gettext_last_set_locale = call_user_func(Settings::get("GETTEXT_INIT"), $langprefs);
            $gettext_last_used_langprefs = $langprefs;
            textdomain(Settings::get("GETTEXT_DOMAIN"));
        }
        return $gettext_last_set_locale;
    }

    private static $gettext_original_domain = null;
    private static $gettext_langprefs_stack = array();

    /**
     * Attempt to switch the language based on the preference array given.
     * Previous language settings will be remembered (in a stack). You SHOULD
     * restore them later by calling gettext_domain_restore.
     */
    public static function gettext_domain_init($langprefs = null)
    {
        # Put the langprefs on the stack.

        if ($langprefs == null)
            $langprefs = array(Settings::get('SITELANG'));
        self::$gettext_langprefs_stack[] = $langprefs;

        if (count(self::$gettext_langprefs_stack) == 1)
        {
            # This is the first time gettext_domain_init is called. In order to
            # properly reinitialize the original settings after gettext_domain_restore
            # is called for the last time, we need to save current textdomain (which
            # should be different than the one which we use - Settings::get("GETTEXT_DOMAIN")).

            self::$gettext_original_domain = textdomain(null);
        }

        # Attempt to change the language. Acquire the actual locale code used
        # (might differ from expected when language was not found).

        $locale_code = self::gettext_set_lang($langprefs);
        return $locale_code;
    }
    public static function gettext_domain_restore()
    {
        # Dismiss the last element on the langpref stack. This is the language
        # which we've been actualy using until now. We want it replaced with
        # the language below it.

        array_pop(self::$gettext_langprefs_stack);

        $size = count(self::$gettext_langprefs_stack);
        if ($size > 0)
        {
            $langprefs = self::$gettext_langprefs_stack[$size - 1];
            self::gettext_set_lang($langprefs);
        }
        else
        {
            # The stack is empty. This means we're going out of OKAPI code and
            # we want the original textdomain reestablished.

            textdomain(self::$gettext_original_domain);
            self::$gettext_original_domain = null;
        }
    }

    /**
     * Internal. This is called always when OKAPI core is included.
     */
    public static function init_internals($allow_cronjobs = true)
    {
        static $init_made = false;
        if ($init_made)
            return;
        ini_set('memory_limit', '256M');
        # The memory limit is - among other - crucial for the maximum size
        # of processable images; see services/logs/images/add.php: max_pixels()
        Db::connect();
        if (Settings::get('TIMEZONE') !== null)
            date_default_timezone_set(Settings::get('TIMEZONE'));
        if (!self::$data_store)
            self::$data_store = new OkapiDataStore();
        if (!self::$server)
            self::$server = new OkapiOAuthServer(self::$data_store);
        if ($allow_cronjobs)
            self::execute_prerequest_cronjobs();
        $init_made = true;
    }

    /**
     * Generate a string of random characters, suitable for keys as passwords.
     * Troublesome characters like '0', 'O', '1', 'l' will not be used.
     * If $user_friendly=true, then it will consist from numbers only.
     */
    public static function generate_key($length, $user_friendly = false)
    {
        if ($user_friendly)
            $chars = "0123456789";
        else
            $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        $max = strlen($chars);
        $key = "";
        for ($i=0; $i<$length; $i++)
        {
            $key .= $chars[rand(0, $max-1)];
        }
        return $key;
    }

    /**
     * Register new OKAPI Consumer, send him an email with his key-pair, etc.
     * This method does not verify parameter values, check if they are in
     * a correct format prior the execution.
     */
    public static function register_new_consumer($appname, $appurl, $email)
    {
        require_once($GLOBALS['rootpath']."okapi/service_runner.php");
        $consumer = new OkapiConsumer(Okapi::generate_key(20), Okapi::generate_key(40),
            $appname, $appurl, $email);
        $sample_cache = OkapiServiceRunner::call("services/caches/search/all",
            new OkapiInternalRequest($consumer, null, array('limit', 1)));
        if (count($sample_cache['results']) > 0)
            $sample_cache_code = $sample_cache['results'][0];
        else
            $sample_cache_code = "CACHECODE";

        # Message for the Consumer.
        ob_start();
        print "This is the key-pair we have created for your application:\n\n";
        print "Consumer Key: $consumer->key\n";
        print "Consumer Secret: $consumer->secret\n\n";
        print "Note: Consumer Secret is needed only when you intend to use OAuth.\n";
        print "You don't need Consumer Secret for Level 1 Authentication.\n\n";
        print "Now you can easily access Level 1 OKAPI methods. E.g.:\n";
        print Settings::get('SITE_URL')."okapi/services/caches/geocache?cache_code=$sample_cache_code&consumer_key=$consumer->key\n\n";
        print "If you plan on using OKAPI for a longer time, then you may want to\n";
        print "subscribe to the OKAPI News blog to stay up-to-date:\n";
        print "http://opencaching-api.blogspot.com/\n\n";
        print "Have fun!\n\n";
        print "-- \n";
        print "OKAPI Team\n";
        Okapi::mail_from_okapi($email, "Your OKAPI Consumer Key", ob_get_clean());

        # Message for the Admins.

        ob_start();
        print "Name: $consumer->name\n";
        print "Developer: $consumer->email\n";
        print ($consumer->url ? "URL: $consumer->url\n" : "");
        print "Consumer Key: $consumer->key\n";
        Okapi::mail_admins("New OKAPI app registered!", ob_get_clean());

        Db::execute("
            insert into okapi_consumers (`key`, name, secret, url, email, date_created)
            values (
                '".Db::escape_string($consumer->key)."',
                '".Db::escape_string($consumer->name)."',
                '".Db::escape_string($consumer->secret)."',
                '".Db::escape_string($consumer->url)."',
                '".Db::escape_string($consumer->email)."',
                now()
            );
        ");
    }

    /** Return the distance between two geopoints, in meters. */
    public static function get_distance($lat1, $lon1, $lat2, $lon2)
    {
        $x1 = (90-$lat1) * 3.14159 / 180;
        $x2 = (90-$lat2) * 3.14159 / 180;
        #
        # min(1, ...) was added below to prevent getting values greater than 1 due
        # to floating point precision limits. See issue #351 for details.
        #
        $d = acos(min(1, cos($x1) * cos($x2) + sin($x1) * sin($x2) * cos(($lon1-$lon2) * 3.14159 / 180))) * 6371000;
        if ($d < 0) $d = 0;
        return $d;
    }

    /**
     * Return an SQL formula for calculating distance between two geopoints.
     * Parameters should be either numberals or strings (SQL field references).
     */
    public static function get_distance_sql($lat1, $lon1, $lat2, $lon2)
    {
        $x1 = "(90-$lat1) * 3.14159 / 180";
        $x2 = "(90-$lat2) * 3.14159 / 180";
        #
        # least(1, ...) was added below to prevent getting values greater than 1 due
        # to floating point precision limits. See issue #351 for details.
        #
        $d = "acos(least(1, cos($x1) * cos($x2) + sin($x1) * sin($x2) * cos(($lon1-$lon2) * 3.14159 / 180))) * 6371000";
        return $d;
    }

    /** Return bearing (float 0..360) from geopoint 1 to 2. */
    public static function get_bearing($lat1, $lon1, $lat2, $lon2)
    {
        if ($lat1 == $lat2 && $lon1 == $lon2)
            return null;
        if ($lat1 == $lat2) $lat1 += 0.0000166;
        if ($lon1 == $lon2) $lon1 += 0.0000166;

        $rad_lat1 = $lat1 / 180.0 * 3.14159;
        $rad_lon1 = $lon1 / 180.0 * 3.14159;
        $rad_lat2 = $lat2 / 180.0 * 3.14159;
        $rad_lon2 = $lon2 / 180.0 * 3.14159;

        $delta_lon = $rad_lon2 - $rad_lon1;
        $bearing = atan2(sin($delta_lon) * cos($rad_lat2),
            cos($rad_lat1) * sin($rad_lat2) - sin($rad_lat1) * cos($rad_lat2) * cos($delta_lon));
        $bearing = 180.0 * $bearing / 3.14159;
        if ( $bearing < 0.0 ) $bearing = $bearing + 360.0;

        return $bearing;
    }

    /** Transform bearing (float 0..360) to simple 2-letter string (N, NE, E, SE, etc.) */
    public static function bearing_as_two_letters($b)
    {
        static $names = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW');
        if ($b === null) return 'n/a';
        return $names[round(($b / 360.0) * 8.0) % 8];
    }

    /** Transform bearing (float 0..360) to simple 3-letter string (N, NNE, NE, ESE, etc.) */
    public static function bearing_as_three_letters($b)
    {
        static $names = array('N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
            'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW');
        if ($b === null) return 'n/a';
        return $names[round(($b / 360.0) * 16.0) % 16];
    }

    /** Escape string for use with XML. See issue 169. */
    public static function xmlescape($string)
    {
        static $pattern = '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u';
        $string = preg_replace($pattern, '', $string);
        return strtr($string, array("<" => "&lt;", ">" => "&gt;", "\"" => "&quot;", "'" => "&apos;", "&" => "&amp;"));
    }

    /**
     * Return object as a standard OKAPI response. The $object will be formatted
     * using one of the default formatters (JSON, JSONP, XML, etc.). Formatter is
     * auto-detected by peeking on the $request's 'format' parameter. In some
     * specific cases, this method can also return the $object itself, instead
     * of OkapiResponse - this allows nesting methods within other methods.
     */
    public static function formatted_response(OkapiRequest $request, &$object)
    {
        if ($request instanceof OkapiInternalRequest && (!$request->i_want_OkapiResponse))
        {
            # If you call a method internally, then you probably expect to get
            # the actual object instead of it's formatted representation.
            return $object;
        }
        $format = $request->get_parameter('format');
        if ($format == null) $format = 'json';
        if (!in_array($format, array('json', 'jsonp', 'xmlmap', 'xmlmap2')))
            throw new InvalidParam('format', "'$format'");
        $callback = $request->get_parameter('callback');
        if ($callback && $format != 'jsonp')
            throw new BadRequest("The 'callback' parameter is reserved to be used with the JSONP output format.");
        if ($format == 'json')
        {
            $response = new OkapiHttpResponse();
            $response->content_type = "application/json; charset=utf-8";
            $response->body = json_encode($object);
            return $response;
        }
        elseif ($format == 'jsonp')
        {
            if (!$callback)
                throw new BadRequest("'callback' parameter is required for JSONP calls");
            if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $callback))
                throw new InvalidParam('callback', "'$callback' doesn't seem to be a valid JavaScript function name (should match /^[a-zA-Z_][a-zA-Z0-9_]*\$/).");
            $response = new OkapiHttpResponse();
            $response->content_type = "application/javascript; charset=utf-8";
            $response->body = $callback."(".json_encode($object).");";
            return $response;
        }
        elseif ($format == 'xmlmap')
        {
            # Deprecated (see issue 128). Keeping this for backward-compatibility.
            $response = new OkapiHttpResponse();
            $response->content_type = "text/xml; charset=utf-8";
            $response->body = self::xmlmap_dumps($object);
            return $response;
        }
        elseif ($format == 'xmlmap2')
        {
            $response = new OkapiHttpResponse();
            $response->content_type = "text/xml; charset=utf-8";
            $response->body = self::xmlmap2_dumps($object);
            return $response;
        }
        else
        {
            # Should not happen (as we do a proper check above).
            throw new Exception();
        }
    }

    private static function _xmlmap_add(&$chunks, &$obj)
    {
        if (is_string($obj))
        {
            $chunks[] = "<string>";
            $chunks[] = self::xmlescape($obj);
            $chunks[] = "</string>";
        }
        elseif (is_int($obj))
        {
            $chunks[] = "<int>$obj</int>";
        }
        elseif (is_float($obj))
        {
            $chunks[] = "<float>$obj</float>";
        }
        elseif (is_bool($obj))
        {
            $chunks[] = $obj ? "<bool>true</bool>" : "<bool>false</bool>";
        }
        elseif (is_null($obj))
        {
            $chunks[] = "<null/>";
        }
        elseif (is_array($obj))
        {
            # Have to check if this is associative or not! Shit. I hate PHP.
            if (array_keys($obj) === range(0, count($obj) - 1))
            {
                # Not assoc.
                $chunks[] = "<list>";
                foreach ($obj as &$item_ref)
                {
                    $chunks[] = "<item>";
                    self::_xmlmap_add($chunks, $item_ref);
                    $chunks[] = "</item>";
                }
                $chunks[] = "</list>";
            }
            else
            {
                # Assoc.
                $chunks[] = "<dict>";
                foreach ($obj as $key => &$item_ref)
                {
                    $chunks[] = "<item key=\"".self::xmlescape($key)."\">";
                    self::_xmlmap_add($chunks, $item_ref);
                    $chunks[] = "</item>";
                }
                $chunks[] = "</dict>";
            }
        }
        else
        {
            # That's a bug.
            throw new Exception("Cannot encode as xmlmap: " + print_r($obj, true));
        }
    }

    private static function _xmlmap2_add(&$chunks, &$obj, $key)
    {
        $attrs = ($key !== null) ? " key=\"".self::xmlescape($key)."\"" : "";
        if (is_string($obj))
        {
            $chunks[] = "<string$attrs>";
            $chunks[] = self::xmlescape($obj);
            $chunks[] = "</string>";
        }
        elseif (is_int($obj))
        {
            $chunks[] = "<number$attrs>$obj</number>";
        }
        elseif (is_float($obj))
        {
            $chunks[] = "<number$attrs>$obj</number>";
        }
        elseif (is_bool($obj))
        {
            $chunks[] = $obj ? "<boolean$attrs>true</boolean>" : "<boolean$attrs>false</boolean>";
        }
        elseif (is_null($obj))
        {
            $chunks[] = "<null$attrs/>";
        }
        elseif (is_array($obj) || ($obj instanceof ArrayObject))
        {
            # Have to check if this is associative or not! Shit. I hate PHP.
            if (is_array($obj) && (array_keys($obj) === range(0, count($obj) - 1)))
            {
                # Not assoc.
                $chunks[] = "<array$attrs>";
                foreach ($obj as &$item_ref)
                {
                    self::_xmlmap2_add($chunks, $item_ref, null);
                }
                $chunks[] = "</array>";
            }
            else
            {
                # Assoc.
                $chunks[] = "<object$attrs>";
                foreach ($obj as $key => &$item_ref)
                {
                    self::_xmlmap2_add($chunks, $item_ref, $key);
                }
                $chunks[] = "</object>";
            }
        }
        else
        {
            # That's a bug.
            throw new Exception("Cannot encode as xmlmap2: " . print_r($obj, true));
        }
    }

    /** Return the object in a serialized version, in the (deprecated) "xmlmap" format. */
    public static function xmlmap_dumps(&$obj)
    {
        $chunks = array();
        self::_xmlmap_add($chunks, $obj);
        return implode('', $chunks);
    }

    /** Return the object in a serialized version, in the "xmlmap2" format. */
    public static function xmlmap2_dumps(&$obj)
    {
        $chunks = array();
        self::_xmlmap2_add($chunks, $obj, null);
        return implode('', $chunks);
    }

    private static $cache_types = array(
        #
        # OKAPI does not expose type IDs. Instead, it uses the following
        # "code words". Only the "primary" cache types are documented.
        # This means that all other types may (in theory) be altered.
        # Cache type may become "primary" ONLY when *all* OC servers recognize
        # that type.
        #
        # Changing this may introduce nasty bugs (e.g. in the replicate module).
        # CONTACT ME BEFORE YOU MODIFY THIS!
        #
        'oc.pl' => array(
            # Primary types (documented, cannot change)
            'Traditional' => 2, 'Multi' => 3, 'Quiz' => 7, 'Virtual' => 4,
            'Event' => 6,
            # Additional types (may get changed)
            'Other' => 1, 'Webcam' => 5,
            'Moving' => 8, 'Podcast' => 9, 'Own' => 10,
        ),
        'oc.de' => array(
            # Primary types (documented, cannot change)
            'Traditional' => 2, 'Multi' => 3, 'Quiz' => 7, 'Virtual' => 4,
            'Event' => 6,
            # Additional types (might get changed)
            'Other' => 1, 'Webcam' => 5,
            'Math/Physics' => 8, 'Moving' => 9, 'Drive-In' => 10,
        )
    );

    /** E.g. 'Traditional' => 2. For unknown names throw an Exception. */
    public static function cache_type_name2id($name)
    {
        $ref = &self::$cache_types[Settings::get('OC_BRANCH')];
        if (isset($ref[$name]))
            return $ref[$name];
        throw new Exception("Method cache_type_name2id called with unsupported cache ".
            "type name '$name'. You should not allow users to submit caches ".
            "of non-primary type.");
    }

    /** E.g. 2 => 'Traditional'. For unknown ids returns "Other". */
    public static function cache_type_id2name($id)
    {
        static $reversed = null;
        if ($reversed == null)
        {
            $reversed = array();
            foreach (self::$cache_types[Settings::get('OC_BRANCH')] as $key => $value)
                $reversed[$value] = $key;
        }
        if (isset($reversed[$id]))
            return $reversed[$id];
        return "Other";
    }

    private static $cache_statuses = array(
        'Available' => 1, 'Temporarily unavailable' => 2, 'Archived' => 3
    );

    /** E.g. 'Available' => 1. For unknown names throws an Exception. */
    public static function cache_status_name2id($name)
    {
        if (isset(self::$cache_statuses[$name]))
            return self::$cache_statuses[$name];
        throw new Exception("Method cache_status_name2id called with invalid name '$name'.");
    }

    /** E.g. 1 => 'Available'. For unknown ids returns 'Archived'. */
    public static function cache_status_id2name($id)
    {
        static $reversed = null;
        if ($reversed == null)
        {
            $reversed = array();
            foreach (self::$cache_statuses as $key => $value)
                $reversed[$value] = $key;
        }
        if (isset($reversed[$id]))
            return $reversed[$id];
        return 'Archived';
    }

    private static $cache_sizes = array(
        'none' => 7,
        'nano' => 8,
        'micro' => 2,
        'small' => 3,
        'regular' => 4,
        'large' => 5,
        'xlarge' => 6,
        'other' => 1,
    );

    /** E.g. 'micro' => 2. For unknown names throw an Exception. */
    public static function cache_size2_to_sizeid($size2)
    {
        if (isset(self::$cache_sizes[$size2]))
            return self::$cache_sizes[$size2];
        throw new Exception("Method cache_size2_to_sizeid called with invalid size2 '$size2'.");
    }

    /** E.g. 2 => 'micro'. For unknown ids returns "other". */
    public static function cache_sizeid_to_size2($id)
    {
        static $reversed = null;
        if ($reversed == null)
        {
            $reversed = array();
            foreach (self::$cache_sizes as $key => $value)
                $reversed[$value] = $key;
        }
        if (isset($reversed[$id]))
            return $reversed[$id];
        return "other";
    }

    /** Maps OKAPI's 'size2' values to opencaching.com (OX) size codes. */
    private static $cache_OX_sizes = array(
        'none' => null,
        'nano' => 1.3,
        'micro' => 2.0,
        'small' => 3.0,
        'regular' => 3.8,
        'large' => 4.6,
        'xlarge' => 4.9,
        'other' => null,
    );

    /**
     * E.g. 'micro' => 2.0, 'other' => null. For unknown names throw an
     * Exception. Note, that this is not a bijection ('none' are 'other' are
     * both null).
     */
    public static function cache_size2_to_oxsize($size2)
    {
        if (array_key_exists($size2, self::$cache_OX_sizes))
            return self::$cache_OX_sizes[$size2];
        throw new Exception("Method cache_size2_to_oxsize called with invalid size2 '$size2'.");
    }

    /**
     * E.g. 'Found it' => 1. For unsupported names throws Exception.
     */
    public static function logtypename2id($name)
    {
        if ($name == 'Found it') return 1;
        if ($name == "Didn't find it") return 2;
        if ($name == 'Comment') return 3;
        if ($name == 'Attended') return 7;
        if ($name == 'Will attend') return 8;
        if (($name == 'Needs maintenance') && (Settings::get('OC_BRANCH') == 'oc.pl')) return 5;
        throw new Exception("logtype2id called with invalid log type argument: $name");
    }

    /** E.g. 1 => 'Found it'. For unknown ids returns 'Comment'. */
    public static function logtypeid2name($id)
    {
        # Various OC sites use different English names, even for primary
        # log types. OKAPI needs to have them the same across *all* OKAPI
        # installations. That's why all known types are hardcoded here.
        # These names are officially documented and may never change!

        # Primary.
        if ($id == 1) return "Found it";
        if ($id == 2) return "Didn't find it";
        if ($id == 3) return "Comment";
        if ($id == 7) return "Attended";
        if ($id == 8) return "Will attend";

        # Other.
        if ($id == 4) return "Moved";
        if ($id == 5) return "Needs maintenance";
        if ($id == 6) return "Maintenance performed";
        if ($id == 9) return "Archived";
        if ($id == 10) return "Ready to search";
        if ($id == 11) return "Temporarily unavailable";
        if ($id == 12) return "OC Team comment";
        if ($id == 13 || $id == 14) return "Locked";

        # Important: This set is not closed. Other types may be introduced
        # in the future. This has to be documented in the public method
        # description.

        return "Comment";
    }

    /**
     * "Fix" user-supplied HTML fetched from the OC database.
     */
    public static function fix_oc_html($html, $object_type)
    {
        /* There are thousands of relative URLs in cache descriptions. We will
         * attempt to find them and fix them. In theory, the "proper" way to do this
         * would be to parse the description into a DOM tree, but that would simply
         * be very hard (and inefficient) to do, since most of the descriptions are
         * not even valid HTML.
         */

        $html = preg_replace(
            "~\b(src|href)=([\"'])(?![a-z0-9_-]+:)~",
            "$1=$2".Settings::get("SITE_URL"),
            $html
        );

        if ($object_type == self::OBJECT_TYPE_CACHE_LOG
            && Settings::get('OC_BRANCH') == 'oc.pl'
        ) {
            # Decode special OCPL entity-encoding produced by logs/submit.
            # See https://github.com/opencaching/okapi/issues/413.
            #
            # This might be restricted to log entries created by OKAPI (that are
            # recorded in okapi_submitted_objects). However, they may have been
            # edited later, so we don't know who created the HTML encoding.

            $html = preg_replace('/&amp;#(38|60|62);/', '&#$1;', $html);
        }

        /* Other things to do in the future:
         *
         * 1. Check for XSS vulerabilities?
         * 2. Transform to a valid (X)HTML?
         */

        return $html;
    }

    function php_ini_get_bytes($variable)
    {
        $value = trim(ini_get($variable));
        if (!preg_match("/^[0-9]+[KM]?$/", $value))
            throw new Exception("Unexpected PHP setting: ".$variable. " = ".$value);
        $value = str_replace('K', '*1024', $value);
        $value = str_replace('M', '*1024*1024', $value);
        $value = eval('return '.$value.';');
        return $value;
    }

    # object types in table okapi_submitted_objects
    const OBJECT_TYPE_CACHE = 1;
    const OBJECT_TYPE_CACHE_DESCRIPTION = 2;
    const OBJECT_TYPE_CACHE_IMAGE = 3;
    const OBJECT_TYPE_CACHE_MP3 = 4;
    const OBJECT_TYPE_CACHE_LOG = 5;           # implemented
    const OBJECT_TYPE_CACHE_LOG_IMAGE = 6;     # implemented
    const OBJECT_TYPE_CACHELIST = 7;
    const OBJECT_TYPE_EMAIL = 8;
    const OBJECT_TYPE_CACHE_REPORT = 9;
}

/** A data caching layer. For slow SQL queries etc. */
class Cache
{
    /**
     * Save object $value under the key $key. Store this object for
     * $timeout seconds. $key must be a string of max 64 characters in length.
     * $value might be any serializable PHP object.
     *
     * If $timeout is null, then the object will be treated as persistent
     * (the Cache will do its best to NEVER remove it).
     */
    public static function set($key, $value, $timeout)
    {
        if ($timeout == null)
        {
            # The current cache implementation is ALWAYS persistent, so we will
            # just replace it with a big value.
            $timeout = 100*365*86400;
        }
        Db::execute("
            replace into okapi_cache (`key`, value, expires)
            values (
                '".Db::escape_string($key)."',
                '".Db::escape_string(gzdeflate(serialize($value)))."',
                date_add(now(), interval '".Db::escape_string($timeout)."' second)
            );
        ");
    }

    /**
     * Scored version of set. Elements set up this way will expire when they're
     * not used.
     */
    public static function set_scored($key, $value)
    {
        Db::execute("
            replace into okapi_cache (`key`, value, expires, score)
            values (
                '".Db::escape_string($key)."',
                '".Db::escape_string(gzdeflate(serialize($value)))."',
                date_add(now(), interval 120 day),
                1.0
            );
        ");
    }

    /** Do 'set' on many keys at once. */
    public static function set_many($dict, $timeout)
    {
        if (count($dict) == 0)
            return;
        if ($timeout == null)
        {
            # The current cache implementation is ALWAYS persistent, so we will
            # just replace it with a big value.
            $timeout = 100*365*86400;
        }
        $entries_escaped = array();
        foreach ($dict as $key => $value)
        {
            $entries_escaped[] = "(
                '".Db::escape_string($key)."',
                '".Db::escape_string(gzdeflate(serialize($value)))."',
                date_add(now(), interval '".Db::escape_string($timeout)."' second)
            )";
        }
        Db::execute("
            replace into okapi_cache (`key`, value, expires)
            values ".implode(", ", $entries_escaped)."
        ");
    }

    /**
     * Retrieve object stored under the key $key. If object does not
     * exist or timeout expired, return null.
     */
    public static function get($key)
    {
        $rs = Db::query("
            select value, score
            from okapi_cache
            where
                `key` = '".Db::escape_string($key)."'
                and expires > now()
        ");
        list($blob, $score) = Db::fetch_row($rs);
        if (!$blob)
            return null;
        if ($score != null)  # Only non-null entries are scored.
        {
            Db::execute("
                insert into okapi_cache_reads (`cache_key`)
                values ('".Db::escape_string($key)."')
            ");
        }
        return unserialize(gzinflate($blob));
    }

    /** Do 'get' on many keys at once. */
    public static function get_many($keys)
    {
        $dict = array();
        $rs = Db::query("
            select `key`, value
            from okapi_cache
            where
                `key` in ('".implode("','", array_map('\okapi\Db::escape_string', $keys))."')
                and expires > now()
        ");
        while ($row = Db::fetch_assoc($rs))
        {
            try
            {
                $dict[$row['key']] = unserialize(gzinflate($row['value']));
            }
            catch (ErrorException $e)
            {
                unset($dict[$row['key']]);
                Okapi::mail_admins("Debug: Unserialize error",
                    "Could not unserialize key '".$row['key']."' from Cache.\n".
                    "Probably something REALLY big was put there and data has been truncated.\n".
                    "Consider upgrading cache table to LONGBLOB.\n\n".
                    "Length of data, compressed: ".strlen($row['value']));
            }
        }
        if (count($dict) < count($keys))
            foreach ($keys as $key)
                if (!isset($dict[$key]))
                    $dict[$key] = null;
        return $dict;
    }

    /**
     * Delete key $key from the cache.
     */
    public static function delete($key)
    {
        self::delete_many(array($key));
    }

    /** Do 'delete' on many keys at once. */
    public static function delete_many($keys)
    {
        if (count($keys) == 0)
            return;
        Db::execute("
            delete from okapi_cache
            where `key` in ('".implode("','", array_map('\okapi\Db::escape_string', $keys))."')
        ");
    }
}

/**
 * Sometimes it is desireable to get the cached contents in a file,
 * instead in a string (i.e. for imagecreatefromgd2). In such cases, you
 * may use this class instead of the Cache class.
 */
class FileCache
{
    public static function get_file_path($key)
    {
        $filename = Okapi::get_var_dir()."/okapi_filecache_".md5($key);
        if (!file_exists($filename))
            return null;
        return $filename;
    }

    /**
     * Note, there is no $timeout (time to live) parameter. Currently,
     * OKAPI will delete every old file after certain amount of time.
     * See CacheCleanupCronJob for details.
     */
    public static function set($key, $value)
    {
        $filename = Okapi::get_var_dir()."/okapi_filecache_".md5($key);
        file_put_contents($filename, $value);
        return $filename;
    }
}

/**
 * Represents an OKAPI web method request.
 *
 * Use this class to get parameters from your request and access
 * Consumer and Token objects. Please note, that request method
 * SHOULD be irrelevant to you: GETs and POSTs are interchangable
 * within OKAPI, and it's up to the caller which one to choose.
 * If you think using GET is "unsafe", then probably you forgot to
 * add OAuth signature requirement (consumer=required) - this way,
 * all the "unsafety" issues of using GET vanish.
 */
abstract class OkapiRequest
{
    public $consumer;
    public $token;
    public $etag;  # see: http://en.wikipedia.org/wiki/HTTP_ETag

    /**
     * Set this to true, for some method to allow you to set higher "limit"
     * parameter than usually allowed. This should be used ONLY by trusted,
     * fast and *cacheable* code!
     */
    public $skip_limits = false;

    /**
     * Return request parameter, or NULL when not found. Use this instead of
     * $_GET or $_POST or $_REQUEST.
     */
    public abstract function get_parameter($name);

    /**
     * Return the list of all request parameters. You should use this method
     * ONLY when you use <import-params/> in your documentation and you want
     * to pass all unknown parameters onto the other method.
     */
    public abstract function get_all_parameters_including_unknown();

    /** Return true, if this requests is to be logged as HTTP request in okapi_stats. */
    public abstract function is_http_request();
}

class OkapiInternalRequest extends OkapiRequest
{
    private $parameters;

    /**
     * Set this to true, if you want this request to be considered as HTTP request
     * in okapi_stats tables. This is useful when running requests through Facade
     * (we want them logged and displayed in weekly report).
     */
    public $perceive_as_http_request = false;

    /**
     * By default, OkapiInsernalRequests work differently than OkapiRequests -
     * they TRY to return PHP objects (like arrays), instead of OkapiResponse
     * objects. Set this to true, if you want this request to work as a regular
     * one - and receive OkapiResponse instead of the PHP object.
     */
    public $i_want_OkapiResponse = false;

    /**
     * You may use "null" values in parameters if you want them skipped
     * (null-ized keys will be removed from parameters).
     */
    public function __construct($consumer, $token, $parameters)
    {
        $this->consumer = $consumer;
        $this->token = $token;
        $this->parameters = array();
        foreach ($parameters as $key => $value)
            if ($value !== null)
                $this->parameters[$key] = $value;
    }

    public function get_parameter($name)
    {
        if (isset($this->parameters[$name]))
            return $this->parameters[$name];
        else
            return null;
    }

    public function get_all_parameters_including_unknown()
    {
        return $this->parameters;
    }

    public function is_http_request() { return $this->perceive_as_http_request; }
}

class OkapiHttpRequest extends OkapiRequest
{
    private $request; /* @var OAuthRequest */
    private $opt_min_auth_level; # 0..3
    private $opt_token_type = 'access'; # "access" or "request"

    public function __construct($options)
    {
        Okapi::init_internals();
        $this->init_request();
        #
        # Parsing options.
        #
        $DEBUG_AS_USERNAME = null;
        foreach ($options as $key => $value)
        {
            switch ($key)
            {
                case 'min_auth_level':
                    if (!in_array($value, array(0, 1, 2, 3)))
                    {
                        throw new Exception("'min_auth_level' option has invalid value: $value");
                    }
                    $this->opt_min_auth_level = $value;
                    break;
                case 'token_type':
                    if (!in_array($value, array("request", "access")))
                    {
                        throw new Exception("'token_type' option has invalid value: $value");
                    }
                    $this->opt_token_type = $value;
                    break;
                case 'DEBUG_AS_USERNAME':
                    $DEBUG_AS_USERNAME = $value;
                    break;
                default:
                    throw new Exception("Unknown option: $key");
                    break;
            }
        }
        if ($this->opt_min_auth_level === null) throw new Exception("Required 'min_auth_level' option is missing.");

        if ($DEBUG_AS_USERNAME != null)
        {
            # Enables debugging Level 2 and Level 3 methods. Should not be committed
            # at any time! If run on production server, make it an error.

            if (!Settings::get('DEBUG'))
            {
                throw new Exception("Attempted to use DEBUG_AS_USERNAME in ".
                    "non-debug environment. Accidental commit?");
            }

            # Lower required authentication to Level 0, to pass the checks.

            $this->opt_min_auth_level = 0;
        }

        #
        # Let's see if the request is signed. If it is, verify the signature.
        # It it's not, check if it isn't against the rules defined in the $options.
        #

        if ($this->get_parameter('oauth_signature'))
        {
            # User is using OAuth.

            # Check for duplicate keys in the parameters. (Datastore doesn't
            # do that on its own, it caused vague server errors - issue #307.)

            $this->get_parameter('oauth_consumer');
            $this->get_parameter('oauth_version');
            $this->get_parameter('oauth_token');
            $this->get_parameter('oauth_nonce');

            # Verify OAuth request.

            list($this->consumer, $this->token) = Okapi::$server->
                verify_request2($this->request, $this->opt_token_type, $this->opt_min_auth_level == 3);
            if ($this->get_parameter('consumer_key') && $this->get_parameter('consumer_key') != $this->get_parameter('oauth_consumer_key'))
                throw new BadRequest("Inproper mixing of authentication types. You used both 'consumer_key' ".
                    "and 'oauth_consumer_key' parameters (Level 1 and Level 2), but they do not match with ".
                    "each other. Were you trying to hack me? ;)");
            if ($this->opt_min_auth_level == 3 && !$this->token)
            {
                throw new BadRequest("This method requires a valid Token to be included (Level 3 ".
                    "Authentication). You didn't provide one.");
            }
        }
        else
        {
            if ($this->opt_min_auth_level >= 2)
            {
                throw new BadRequest("This method requires OAuth signature (Level ".
                    $this->opt_min_auth_level." Authentication). You didn't sign your request.");
            }
            else
            {
                $consumer_key = $this->get_parameter('consumer_key');
                if ($consumer_key)
                {
                    $this->consumer = Okapi::$data_store->lookup_consumer($consumer_key);
                    if (!$this->consumer) {
                        throw new InvalidParam('consumer_key', "Consumer does not exist.");
                    }
                }
                if (($this->opt_min_auth_level == 1) && (!$this->consumer))
                    throw new BadRequest("This method requires the 'consumer_key' argument (Level 1 ".
                        "Authentication). You didn't provide one.");
            }
        }

        if (is_object($this->consumer) && $this->consumer->hasFlag(OkapiConsumer::FLAG_KEY_REVOKED)) {
            throw new InvalidParam(
                'consumer_key',
                "Your application was denied access to the " .
                Okapi::get_normalized_site_name() . " site " .
                "(this consumer key has been revoked)."
            );
        }

        if (is_object($this->consumer) && $this->consumer->hasFlag(OkapiConsumer::FLAG_SKIP_LIMITS)) {
            $this->skip_limits = true;
        }

        #
        # Prevent developers from accessing request parameters with PHP globals.
        # Remember, that OKAPI requests can be nested within other OKAPI requests!
        # Search the code for "new OkapiInternalRequest" to see examples.
        #

        $_GET = $_POST = $_REQUEST = null;

        # When debugging, simulate as if been run using a proper Level 3 Authentication.

        if ($DEBUG_AS_USERNAME != null)
        {
            # Note, that this will override any other valid authentication the
            # developer might have issued.

            $debug_user_id = Db::select_value("select user_id from user where username='".
                Db::escape_string($options['DEBUG_AS_USERNAME'])."'");
            if ($debug_user_id == null)
                throw new Exception("Invalid user name in DEBUG_AS_USERNAME: '".$options['DEBUG_AS_USERNAME']."'");
            $this->consumer = new OkapiDebugConsumer();
            $this->token = new OkapiDebugAccessToken($debug_user_id);
        }

        # Read the ETag.

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
            $this->etag = $_SERVER['HTTP_IF_NONE_MATCH'];
    }

    private function init_request()
    {
        $this->request = OAuthRequest::from_request();

        /* Verify if the request was issued with proper HTTP method. */

        if (!in_array(
            $this->request->get_normalized_http_method(),
            array('GET', 'POST')
        )) {
            throw new BadRequest("Use GET and POST methods only.");
        }

        /* Verify if the request was issued with proper okapi_base_url. */

        $url = $this->request->get_normalized_http_url();
        $allowed = false;
        foreach (Okapi::get_allowed_base_urls() as $allowed_prefix) {
            if (strpos($url, $allowed_prefix) === 0) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            throw new BadRequest(
                "Unrecognized base URL prefix! See `okapi_base_urls` field ".
                "in the `services/apisrv/installation` method. (Recommended ".
                "base URL to use is '".Okapi::get_recommended_base_url()."'.)"
            );
        }
    }

    /**
     * Return request parameter, or NULL when not found. Use this instead of
     * $_GET or $_POST or $_REQUEST.
     */
    public function get_parameter($name)
    {
        $value = $this->request->get_parameter($name);

        # Default implementation of OAuthRequest allows arrays to be passed with
        # multiple references to the same variable ("a=1&a=2&a=3"). This is invalid
        # in OKAPI and should be reported back. See issue 85:
        # https://github.com/opencaching/okapi/issues/85

        if (is_array($value))
            throw new InvalidParam($name, "Make sure you are using '$name' no more than ONCE in your URL.");
        return $value;
    }

    public function get_all_parameters_including_unknown()
    {
        return $this->request->get_parameters();
    }

    public function is_http_request() { return true; }
}
