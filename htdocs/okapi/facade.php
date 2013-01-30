<?

namespace okapi;

# OKAPI Framework -- Wojciech Rygielski <rygielski@mimuw.edu.pl>

# Include this file if you want to use OKAPI's services with any
# external code (your service calls will appear under the name "Facade"
# in the weekly OKAPI usage report).

# Note, that his is the *ONLY* internal OKAPI file that is guaranteed
# to stay backward-compatible (I'm speaking about INTERNAL files here,
# all OKAPI methods will stay compatible forever). If you want to use
# something that has not been exposed through the Facade class, contact
# OKAPI developers, we will add it here.

# Including this file will initialize OKAPI Framework with its default
# exception and error handlers. OKAPI is strict about PHP warnings and
# notices. You might need to temporarily disable the error handler in
# order to get it to work with some legacy code. Do this by calling
# OkapiErrorHandler::disable() BEFORE calling the "buggy" code, and
# OkapiErrorHandler::reenable() AFTER returning from it.


use Exception;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\OkapiFacadeConsumer;
use okapi\OkapiFacadeAccessToken;

require_once($GLOBALS['rootpath']."okapi/core.php");
OkapiErrorHandler::$treat_notices_as_errors = true;
require_once($GLOBALS['rootpath']."okapi/service_runner.php");
Okapi::init_internals();

/**
 * Use this class to access OKAPI's services from external code (i.e. OC code).
 */
class Facade
{
	/**
	 * Perform OKAPI service call, signed by internal 'facade' consumer key, and return the result
	 * (this will be PHP object or OkapiHttpResponse, depending on the method). Use this method
	 * whenever you need to access OKAPI services from within OC code. If you want to simulate
	 * Level 3 Authentication, you should supply user's internal ID (the second parameter).
	 */
	public static function service_call($service_name, $user_id_or_null, $parameters)
	{
		$request = new OkapiInternalRequest(
			new OkapiFacadeConsumer(),
			($user_id_or_null !== null) ? new OkapiFacadeAccessToken($user_id_or_null) : null,
			$parameters
		);
		$request->perceive_as_http_request = true;
		return OkapiServiceRunner::call($service_name, $request);
	}
	
	/**
	 * This works like service_call with two exceptions: 1. It passes all your
	 * current HTTP request headers to OKAPI (which can make use of them in
	 * terms of caching), 2. It outputs the service response directly, instead
	 * of returning it.
	 */
	public static function service_display($service_name, $user_id_or_null, $parameters)
	{
		$request = new OkapiInternalRequest(
			new OkapiFacadeConsumer(),
			($user_id_or_null !== null) ? new OkapiFacadeAccessToken($user_id_or_null) : null,
			$parameters
		);
		$request->perceive_as_http_request = true;
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
			$request->etag = $_SERVER['HTTP_IF_NONE_MATCH'];
		$response = OkapiServiceRunner::call($service_name, $request);
		$response->display();
	}
	
	/**
	 * Create a search set from a temporary table. This is very similar to
	 * the "services/caches/search/save" method, but allows OC server to
	 * include its own result instead of using OKAPI's search options. The
	 * $temp_table should be a valid name of a temporary table with the
	 * following (or similar) structure:
	 *
	 *   create temporary table temp_12345 (
	 *     cache_id integer primary key
	 *   ) engine=memory;
	 */
	public static function import_search_set($temp_table, $min_store, $max_ref_age)
	{
		require_once 'services/caches/search/save.php';
		$tables = array('caches', $temp_table);
		$where_conds = array(
			$temp_table.".cache_id = caches.cache_id",
			'caches.status in (1,2,3)',
		);
		return \okapi\services\caches\search\save\WebService::get_set(
			$tables, $where_conds, $min_store, $max_ref_age
		);
	}
}
