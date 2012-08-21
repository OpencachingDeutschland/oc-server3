<?php

namespace okapi;

# OKAPI Framework -- Wojciech Rygielski <rygielski@mimuw.edu.pl>

# Including this file will initialize OKAPI Framework with its default
# exception and error handlers. OKAPI is strict about PHP warnings and
# notices. You might need to temporarily disable the error handler in
# order to get it to work in some legacy code. Do this by calling
# OkapiErrorHandler::disable() BEFORE calling the "buggy" code, and
# OkapiErrorHandler::reenable() AFTER returning from it.

# When I was installing it for the first time on the opencaching.pl
# site, I needed to change some minor things in order to get it to work
# properly, i.e., turn some "die()"-like statements into exceptions.
# Contact me for details.

# I hope this will come in handy...  - WR.

use Exception;
use ErrorException;
use OAuthServerException;
use OAuthServer400Exception;
use OAuthServer401Exception;
use OAuthMissingParameterException;
use OAuthConsumer;
use OAuthToken;
use OAuthServer;
use OAuthSignatureMethod_HMAC_SHA1;
use OAuthRequest;
use okapi\cronjobs\CronJobController;

/** Return an array of email addresses which always get notified on OKAPI errors. */
function get_admin_emails()
{
	$emails = array(
		isset($GLOBALS['sql_errormail']) ? $GLOBALS['sql_errormail'] : 'root@localhost',
	);
	if (class_exists("okapi\\Settings"))
	{
		try
		{
			foreach (Settings::get('EXTRA_ADMINS') as $email)
				if (!in_array($email, $emails))
					$emails[] = $email;
		}
		catch (Exception $e) { /* pass */ }
	}
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
		$extras['more_info'] = $GLOBALS['absolute_server_URI']."okapi/introduction.html#errors";
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
			header("Content-Type: text/plain; charset=utf-8");
			
			print $e->getOkapiJSON();
		}
		elseif ($e instanceof BadRequest)
		{
			# Intentionally thrown from within the OKAPI method code.
			# Consumer (aka external developer) had something wrong with his
			# request and we want him to know that.
			
			header("HTTP/1.0 400 Bad Request");
			header("Access-Control-Allow-Origin: *");
			header("Content-Type: text/plain; charset=utf-8");
			
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
			
			if (isset($GLOBALS['debug_page']) && $GLOBALS['debug_page'])
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
				$admin_email = implode(", ", get_admin_emails());
				$sender_email = isset($GLOBALS['emailaddr']) ? $GLOBALS['emailaddr'] : 'root@localhost';
				mail(
					$admin_email,
					"OKAPI Method Error - ".(
						isset($GLOBALS['absolute_server_URI'])
						? $GLOBALS['absolute_server_URI'] : "unknown location"
					),
					"OKAPI caught the following exception while executing API method request.\n".
					"This is an error in OUR code and should be fixed. Please contact the\n".
					"developer of the module that threw this error. Thanks!\n\n".
					$exception_info,
					"Content-Type: text/plain; charset=utf-8\n".
					"From: OKAPI <$sender_email>\n".
					"Reply-To: $sender_email\n"
					);
			}
		}
	}
	
	public static function get_exception_info($e)
	{
		$exception_info = "===== ERROR MESSAGE =====\n".trim($e->getMessage())."\n=========================\n\n";
		if ($e instanceof FatalError)
		{
			# This one doesn't have a stack trace. It is fed directly to OkapiExceptionHandler::handle
			# by OkapiErrorHandler::handle_shutdown. Instead of printing trace, we will just print
			# the file and line.
			
			$exception_info .= "File: ".$e->getFile()."\nLine: ".$e->getLine()."\n\n";
		}
		else
		{
			$exception_info .= "--- Stack trace ---\n".$e->getTraceAsString()."\n\n";
		}
		
		$exception_info .= (isset($_SERVER['REQUEST_URI']) ? "--- OKAPI method called ---\n".
			preg_replace("/([?&])/", "\n$1", $_SERVER['REQUEST_URI'])."\n\n" : "");
		$exception_info .= "--- Request headers ---\n".implode("\n", array_map(
			function($k, $v) { return "$k: $v"; },
			array_keys(getallheaders()), array_values(getallheaders())
		));
		
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
		if ($severity == E_STRICT) return false;
		if (($severity == E_NOTICE || $severity == E_DEPRECATED) &&
			!self::$treat_notices_as_errors)
		{
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
	private $paramName;
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
# Database access layer.
#

/** Database access class. Use this instead of mysql_query, sql or sqlValue. */
class Db
{
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

	public static function select_all($query)
	{
		$rows = array();
		self::select_and_push($query, $rows);
		return $rows;
	}
	
	public static function select_and_push($query, & $arr, $keyField = null)
	{
		$rs = self::query($query);
		while (true)
		{
			$row = mysql_fetch_assoc($rs);
			if ($row === false)
				break;
			if ($keyField == null)
				$arr[] = $row;
			else
				$arr[$row[$keyField]] = $row;
		}
		mysql_free_result($rs);
	}

	public static function select_value($query)
	{
		$column = self::select_column($query);
		if ($column == null)
			return null;
		if (count($column) == 1)
			return $column[0];
		throw new DbException("Invalid query. Db::select_value returned more than one row for:\n\n".$query."\n");
	}
	
	public static function select_column($query)
	{
		$column = array();
		$rs = self::query($query);
		while (true)
		{
			$values = mysql_fetch_array($rs);
			if ($values === false)
				break;
			array_push($column, $values[0]);
		}
		mysql_free_result($rs);
		return $column;
	}
	
	public static function last_insert_id()
	{
		return mysql_insert_id();
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
		$rs = mysql_query($query);
		if (!$rs)
		{
			throw new DbException("SQL Error ".mysql_errno().": ".mysql_error()."\n\nThe query was:\n".$query."\n");
		}
		return $rs;
	}
}

#
# Including OAuth internals. Preparing OKAPI Consumer and Token classes.
#

require_once('oauth.php');

class OkapiConsumer extends OAuthConsumer
{
	public $name;
	public $url;
	public $email;
	
	public function __construct($key, $secret, $name, $url, $email)
	{
		$this->key = $key;
		$this->secret = $secret;
		$this->name = $name;
		$this->url = $url;
		$this->email = $email;
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

require_once('settings.php');
require_once('datastore.php');

class OkapiHttpResponse
{
	public $status = "200 OK";
	public $content_type = "text/plain; charset=utf-8";
	public $content_disposition = null;
	public $allow_gzip = true;
	public $connection_close = false;
	
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
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0");
		if ($this->connection_close)
			header("Connection: close");
		if ($this->content_disposition)
			header("Content-Disposition: ".$this->content_disposition);
		
		# Make sure that gzip is supported by the client.
		$try_gzip = $this->allow_gzip;
		if (empty($_SERVER["HTTP_ACCEPT_ENCODING"]) || (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") === false))
			$try_gzip = false;

		# We will gzip the data ourselves, while disabling gziping by Apache. This way, we can
		# set the Content-Length correctly which is handy in some scenarios.
		
		if ($try_gzip && is_string($this->body))
		{
			header("Content-Encoding: gzip");
			$gzipped = gzencode($this->body, 5, true);
			header("Content-Length: ".strlen($gzipped));
			print $gzipped;
		}
		else
		{
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

class OkapiLock
{
	private $lock;
	
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
			$lockfile = Okapi::get_var_dir()."/okapi-lock-".$name;
			if (!file_exists($lockfile))
			{
				$fp = fopen($lockfile, "wb");
				fclose($fp);
			}
			$this->lock = sem_get(fileinode($lockfile));
		}
	}
	
	public function acquire()
	{
		if ($this->lock !== null)
			sem_acquire($this->lock);
	}
	
	public function release()
	{
		if ($this->lock !== null)
			sem_release($this->lock);
	}
}

/** Container for various OKAPI functions. */
class Okapi
{
	public static $data_store;
	public static $server;
	public static $revision = 413; # This gets replaced in automatically deployed packages
	private static $okapi_vars = null;
	
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
			while ($row = mysql_fetch_assoc($rs))
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
				'".mysql_real_escape_string($varname)."',
				'".mysql_real_escape_string($value)."');
		");
		self::$okapi_vars[$varname] = $value;
	}
	
	/** Return true if the server is running in a debug mode. */
	public static function debug_mode()
	{
		return (isset($GLOBALS['debug_page']) && $GLOBALS['debug_page']);
	}
	
	/** Send an email message to local OKAPI administrators. */
	public static function mail_admins($subject, $message)
	{
		self::mail_from_okapi(get_admin_emails(), $subject, $message);
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
		$sender_email = isset($GLOBALS['emailaddr']) ? $GLOBALS['emailaddr'] : 'root@localhost';
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
		return isset($GLOBALS['dynbasepath']) ? $GLOBALS['dynbasepath'] : "/tmp";
	}
	
	/**
	 * Get an array of all site-specific attributes in the following format:
	 * $arr[<id_of_the_attribute>][<language_code>] = <attribute_name>.
	 */
	public static function get_all_atribute_names()
	{
		if (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# OCPL branch uses cache_attrib table to store attribute names. It has
			# different structure than the OCDE cache_attrib table. OCPL does not
			# have translation tables.
			
			$rs = Db::query("select id, language, text_long from cache_attrib order by id");
		}
		else
		{
			# OCDE branch uses translation tables. Let's make a select which will
			# produce results compatible with the one above.
			
			$rs = Db::query("
				select
					ca.id,
					stt.lang as language,
					stt.text as text_long
				from
					cache_attrib ca,
					sys_trans_text stt
				where ca.trans_id = stt.trans_id
				order by ca.id
			");
		}
			
		$dict = array();
		while ($row = mysql_fetch_assoc($rs)) {
			$dict[$row['id']][strtolower($row['language'])] = $row['text_long'];
		}
		return $dict;
	}
	
	/** Returns something like "OpenCaching.PL" or "OpenCaching.DE". */
	public static function get_normalized_site_name($site_url = null)
	{
		if ($site_url == null)
			$site_url = $GLOBALS['absolute_server_URI'];
		$matches = null;
		if (preg_match("#^https?://(www.)?opencaching.([a-z.]+)/$#", $site_url, $matches)) {
			return "OpenCaching.".strtoupper($matches[2]);
		} else {
			return "DEVELSITE";
		}
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
			require_once 'cronjobs.php';
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
			require_once 'cronjobs.php';
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
		ini_set('memory_limit', '128M');
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
		include_once 'service_runner.php';
		$consumer = new OkapiConsumer(Okapi::generate_key(20), Okapi::generate_key(40),
			$appname, $appurl, $email);
		$sample_cache = OkapiServiceRunner::call("services/caches/search/all",
			new OkapiInternalRequest($consumer, null, array('limit', 1)));
		if (count($sample_cache['results']) > 0)
			$sample_cache_code = $sample_cache['results'][0];
		else
			$sample_cache_code = "CACHECODE";
		$sender_email = isset($GLOBALS['emailaddr']) ? $GLOBALS['emailaddr'] : 'root@localhost';
		
		# Message for the Consumer.
		ob_start();
		print "This is the key-pair we've generated for your application:\n\n";
		print "Consumer Key: $consumer->key\n";
		print "Consumer Secret: $consumer->secret\n\n";
		print "Note: Consumer Secret is needed only when you intend to use OAuth.\n";
		print "You don't need Consumer Secret for Level 1 Authentication.\n\n";
		print "Now you may easily access Level 1 methods of OKAPI! For example:\n";
		print $GLOBALS['absolute_server_URI']."okapi/services/caches/geocache?cache_code=$sample_cache_code&consumer_key=$consumer->key\n\n";
		print "If you plan on using OKAPI for a longer time, then you should subscribe\n";
		print "to the OKAPI News blog to stay up-to-date. Check it out here:\n";
		print "http://opencaching-api.blogspot.com/\n\n";
		print "Have fun!";
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
				'".mysql_real_escape_string($consumer->key)."',
				'".mysql_real_escape_string($consumer->name)."',
				'".mysql_real_escape_string($consumer->secret)."',
				'".mysql_real_escape_string($consumer->url)."',
				'".mysql_real_escape_string($consumer->email)."',
				now()
			);
		");
	}
	
	/** Return the distance between two geopoints, in meters. */
	public static function get_distance($lat1, $lon1, $lat2, $lon2)
	{
		$x1 = (90-$lat1) * 3.14159 / 180;
		$x2 = (90-$lat2) * 3.14159 / 180;
		$d = acos(cos($x1) * cos($x2) + sin($x1) * sin($x2) * cos(($lon1-$lon2) * 3.14159 / 180)) * 6371000;
		if ($d < 0) $d = 0;
		return $d;
	}
	
	/**
	 * Return an SQL formula for calculating distance between two geopoints.
	 * Parameters should be either numberals or strings (SQL field references).
	 */
	public function get_distance_sql($lat1, $lon1, $lat2, $lon2)
	{
		$x1 = "(90-$lat1) * 3.14159 / 180";
		$x2 = "(90-$lat2) * 3.14159 / 180";
		$d = "acos(cos($x1) * cos($x2) + sin($x1) * sin($x2) * cos(($lon1-$lon2) * 3.14159 / 180)) * 6371000";
		return $d;
	}

	/** Return bearing (float 0..360) from geopoint 1 to 2. */
	public function get_bearing($lat1, $lon1, $lat2, $lon2)
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
	function bearing_as_two_letters($b)
	{
		static $names = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW');
		if ($b === null) return 'n/a';
		return $names[round(($b / 360.0) * 8.0) % 8];
	}
	
	/** Transform bearing (float 0..360) to simple 3-letter string (N, NNE, NE, ESE, etc.) */
	function bearing_as_three_letters($b)
	{
		static $names = array('N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
			'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW');
		if ($b === null) return 'n/a';
		return $names[round(($b / 360.0) * 16.0) % 16];
	}
	
	/** Escape string for use with XML. */
	public static function xmlentities($string)
	{
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
		if ($request instanceof OkapiInternalRequest && ($request->i_want_okapi_response == false))
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
			$chunks[] = self::xmlentities($obj);
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
					$chunks[] = "<item key=\"".self::xmlentities($key)."\">";
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
		$attrs = ($key !== null) ? " key=\"".self::xmlentities($key)."\"" : "";
		if (is_string($obj))
		{
			$chunks[] = "<string$attrs>";
			$chunks[] = self::xmlentities($obj);
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
		elseif (is_array($obj))
		{
			# Have to check if this is associative or not! Shit. I hate PHP.
			if (array_keys($obj) === range(0, count($obj) - 1))
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
			throw new Exception("Cannot encode as xmlmap2: " + print_r($obj, true));
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
		# Primary types
		'Traditional' => 2, 'Multi' => 3, 'Quiz' => 7, 'Virtual' => 4,
		# Additional types - these should include ALL types used in
		# ANY of the opencaching installations. Contact me if you want to modify this.
		'Event' => 6, 'Webcam' => 5, 'Moving' => 8, 'Own' => 9, 'Other' => 1,
	);
	
	private static $cache_statuses = array(
		'Available' => 1, 'Temporarily unavailable' => 2, 'Archived' => 3
	);
	
	/** E.g. 'Traditional' => 2. For unknown names throw an Exception. */
	public static function cache_type_name2id($name)
	{
		if (isset(self::$cache_types[$name]))
			return self::$cache_types[$name];
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
			foreach (self::$cache_types as $key => $value)
				$reversed[$value] = $key;
		}
		if (isset($reversed[$id]))
			return $reversed[$id];
		return "Other";
	}
	
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
	
	/**
	 * E.g. 'Found it' => 1. For unsupported names throws Exception.
	 */
	public static function logtypename2id($name)
	{
		if ($name == 'Found it') return 1;
		if ($name == "Didn't find it") return 2;
		if ($name == 'Comment') return 3;
		if (($name == 'Needs maintenance') && (Settings::get('SUPPORTS_LOGTYPE_NEEDS_MAINTENANCE')))
			return 5;
		throw new Exception("logtype2id called with invalid log type argument: $name");
	}
	
	/** E.g. 1 => 'Found it'. For unknown ids returns 'Comment'. */
	public static function logtypeid2name($id)
	{
		# Various OC nodes use different English names, even for primary
		# log types. OKAPI needs to have them the same across *all* OKAPI
		# installations. That's why these 3 are hardcoded (and should
		# NEVER be changed).
		
		if ($id == 1) return "Found it";
		if ($id == 2) return "Didn't find it";
		if ($id == 3) return "Comment";
		
		static $other_types = null;
		if ($other_types === null)
		{
			# All the other log types are non-standard ones. Their names have to
			# be delivered from database tables. In general, OKAPI threat such
			# non-standard log entries as comments, but - perhaps - external
			# applications can use it in some other way. We decided to expose
			# ENGLISH (and ONLY English) names of such log entry types. We also
			# advise external developers to treat unknown log entry types as
			# comments inside their application.
			
			if (Settings::get('OC_BRANCH') == 'oc.pl')
			{
				# OCPL uses log_types table to store log type names.
				$rs = Db::query("select id, en from log_types");
			}
			else
			{
				# OCDE uses log_types with translation tables.
				
				$rs = Db::query("
					select
						lt.id,
						stt.text as en
					from
						log_types lt,
						sys_trans_text stt
					where
						lt.trans_id = stt.trans_id
						and stt.lang = 'en'
				");
			}
			$other_types = array();
			while ($row = mysql_fetch_assoc($rs))
				$other_types[$row['id']] = $row['en'];
		}
		
		if (isset($other_types[$id]))
			return $other_types[$id];
		
		return "Comment";
	}
}

/** A data caching layer. For slow SQL queries etc. */
class Cache
{
	/**
	 * Save object $value under the key $key. Store this object for
	 * $timeout seconds. $key must be a string of max 32 characters in length.
	 * $value might be any serializable PHP object.
	 */
	public static function set($key, $value, $timeout)
	{
		Db::execute("
			replace into okapi_cache (`key`, value, expires)
			values (
				'".mysql_real_escape_string($key)."',
				'".mysql_real_escape_string(gzdeflate(serialize($value)))."',
				date_add(now(), interval '".mysql_real_escape_string($timeout)."' second)
			);
		");
	}
	
	/** Do 'set' on many keys at once. */
	public static function set_many($dict, $timeout)
	{
		if (count($dict) == 0)
			return;
		$entries = array();
		foreach ($dict as $key => $value)
		{
			$entries[] = "(
				'".mysql_real_escape_string($key)."',
				'".mysql_real_escape_string(gzdeflate(serialize($value)))."',
				date_add(now(), interval '".mysql_real_escape_string($timeout)."' second)
			)";
		}
		Db::execute("
			replace into okapi_cache (`key`, value, expires)
			values ".implode(", ", $entries)."
		");
	}
	
	/**
	 * Retrieve object stored under the key $key. If object does not
	 * exist or timeout expired, return null.
	 */
	public static function get($key)
	{
		$blob = Db::select_value("
			select value
			from okapi_cache
			where
				`key` = '".mysql_real_escape_string($key)."'
				and expires > now()
		");
		if (!$blob)
			return null;
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
				`key` in ('".implode("','", array_map('mysql_real_escape_string', $keys))."')
				and expires > now()
		");
		while ($row = mysql_fetch_assoc($rs))
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
			where `key` in ('".implode("','", array_map('mysql_real_escape_string', $keys))."')
		");
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
	
	/**
	 * Return request parameter, or NULL when not found. Use this instead of
	 * $_GET or $_POST or $_REQUEST.
	 */
	public abstract function get_parameter($name);
	
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
	 * Set this to true, if you want to receive OkapiResponse instead of
	 * the actual object.
	 */
	public $i_want_okapi_response = false;
	
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
			
			if (!Okapi::debug_mode())
			{
				throw new Exception("Attempted to set DEBUG_AS_USERNAME set in ".
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
			# User is using OAuth. There is a cronjob scheduled to run every 5 minutes and
			# delete old Request Tokens and Nonces. We may assume that cleanup was executed
			# not more than 5 minutes ago.
			
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
					if (!$this->consumer)
						throw new InvalidParam('consumer_key', "Consumer does not exist.");
				}
				if (($this->opt_min_auth_level == 1) && (!$this->consumer))
					throw new BadRequest("This method requires the 'consumer_key' argument (Level 1 ".
						"Authentication). You didn't provide one.");
			}
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
				mysql_real_escape_string($options['DEBUG_AS_USERNAME'])."'");
			if ($debug_user_id == null)
				throw new Exception("Invalid user name in DEBUG_AS_USERNAME: '".$options['DEBUG_AS_USERNAME']."'");
			$this->consumer = new OkapiDebugConsumer();
			$this->token = new OkapiDebugAccessToken($debug_user_id);
		}
	}
	
	private function init_request()
	{
		$this->request = OAuthRequest::from_request();
		if (!in_array($this->request->get_normalized_http_method(),
			array('GET', 'POST')))
		{
			throw new BadRequest("Use GET and POST methods only.");
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
		# http://code.google.com/p/opencaching-api/issues/detail?id=85
		
		if (is_array($value))
			throw new InvalidParam($name, "Make sure you are using '$name' no more than ONCE in your URL.");
		return $value;
	}
	
	public function is_http_request() { return true; }
}
