<?

namespace okapi;

use Exception;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\OkapiFacadeConsumer;
use okapi\OkapiFacadeAccessToken;

require_once('core.php');
require_once('service_runner.php');

/**
 * Use this class to access OKAPI from OC code. This is the *ONLY* internal OKAPI file that is
 * guaranteed to stay backward-compatible*! You SHOULD NOT include any other okapi file in your
 * code. If you want to use something that has not been exposed through the Facade class,
 * inform OKAPI developers, we will add it.
 *
 *   * - notice that we are talking about INTERNAL files here. Of course, all OKAPI methods
 *       (accessed via HTTP) will stay compatible forever (if possible).
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
		// WRTODO: make this count as HTTP call?
		$request = new OkapiInternalRequest(
			new OkapiFacadeConsumer(),
			($user_id_or_null !== null) ? new OkapiFacadeAccessToken($user_id_or_null) : null,
			$parameters
		);
		$request->perceive_as_http_request = true;
		return OkapiServiceRunner::call($service_name, $request);
	}
}